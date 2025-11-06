<?php

namespace App\Services;

use App\Events\OrderUpdated;
use App\Models\AuditLog;
use App\Models\Menu;
use App\Models\MenuOption;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Table;
use App\Models\TableSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

// Central place for order lifecycle logic
class OrderService
{
    private const TAX_RATE = 0.1;
    private const SERVICE_RATE = 0.05;

    public function createOrder(string $tableCode, array $items, ?string $notes, string $paymentMethod): Order
    {
        return DB::transaction(function () use ($tableCode, $items, $notes, $paymentMethod) {
            $table = Table::where('code', $tableCode)->firstOrFail();

            $session = $table->sessions()
                ->where('status', TableSession::STATUS_ACTIVE)
                ->latest('started_at')
                ->first();

            if (! $session) {
                $session = $table->sessions()->create([
                    'started_at' => now(),
                    'status' => TableSession::STATUS_ACTIVE,
                ]);
            }

            $subtotal = 0;
            $orderItems = [];

            foreach ($items as $item) {
                /** @var Menu $menu */
                $menu = Menu::findOrFail($item['menu_id']);
                $selectedOptions = $this->collectOptions($menu, $item['options'] ?? []);

                $linePrice = $menu->price + $selectedOptions->sum(fn (MenuOption $option) => $option->extra_price);
                $lineTotal = $linePrice * $item['qty'];
                $subtotal += $lineTotal;

                if ($menu->stock !== null && $menu->stock < $item['qty']) {
                    throw new \RuntimeException("Stok {$menu->name} tidak mencukupi.");
                }

                if ($menu->stock !== null) {
                    $menu->decrement('stock', $item['qty']);
                }

                $orderItems[] = [
                    'menu_id' => $menu->id,
                    'qty' => $item['qty'],
                    'price' => $linePrice,
                    'options_json' => $selectedOptions->map(fn (MenuOption $option) => [
                        'id' => $option->id,
                        'name' => $option->name,
                        'extra_price' => $option->extra_price,
                    ])->values()->all(),
                    'notes' => $item['notes'] ?? null,
                ];
            }

            $tax = round($subtotal * self::TAX_RATE, 2);
            $service = round($subtotal * self::SERVICE_RATE, 2);
            $grandTotal = $subtotal + $tax + $service;

            $order = $session->orders()->create([
                'code' => 'RK-' . Str::upper(Str::random(8)),
                'subtotal' => $subtotal,
                'tax' => $tax,
                'service_charge' => $service,
                'grand_total' => $grandTotal,
                'status' => Order::STATUS_PLACED,
                'notes' => $notes,
            ]);

            foreach ($orderItems as $payload) {
                $order->items()->create($payload);
            }

            $payment = $order->payments()->create([
                'method' => $paymentMethod,
                'amount' => $grandTotal,
                'status' => Payment::STATUS_PENDING,
            ]);

            $this->logAudit('order_created', $order, [
                'payment_id' => $payment->id,
                'items_count' => count($orderItems),
            ]);

            OrderUpdated::dispatch($order->fresh(['items', 'tableSession.table']));

            return $order->load(['items.menu', 'payments']);
        });
    }

    public function updateOrderStatus(Order $order, string $status): Order
    {
        $order->update(['status' => $status]);

        if ($status === Order::STATUS_PAID) {
            $order->tableSession->update(['status' => TableSession::STATUS_ACTIVE]);
        }

        if ($status === Order::STATUS_CLOSED) {
            $order->tableSession->update([
                'status' => TableSession::STATUS_INACTIVE,
                'closed_at' => now(),
            ]);
        }

        $this->logAudit('order_status_updated', $order, ['status' => $status]);

        OrderUpdated::dispatch($order->fresh(['items', 'payments', 'tableSession.table']));

        return $order->refresh();
    }

    public function markPaymentSuccess(Payment $payment, ?string $providerRef = null): Payment
    {
        $payment->update([
            'status' => Payment::STATUS_SUCCESS,
            'provider_ref' => $providerRef,
            'paid_at' => now(),
        ]);

        $order = $payment->order;
        if ($order->status !== Order::STATUS_PAID) {
            $this->updateOrderStatus($order, Order::STATUS_PAID);
        }

        $this->logAudit('payment_success', $order, ['payment_id' => $payment->id]);

        return $payment->refresh();
    }

    private function collectOptions(Menu $menu, array $optionIds)
    {
        return $menu->options()->whereIn('id', $optionIds)->get();
    }

    private function logAudit(string $action, Order $order, array $meta = []): void
    {
        AuditLog::create([
            'actor_id' => Auth::id(),
            'action' => $action,
            'entity' => 'order',
            'entity_id' => $order->id,
            'meta_json' => $meta,
            'created_at' => now(),
        ]);
    }
}

