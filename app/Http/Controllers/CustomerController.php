<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\Table;
use App\Models\TableSession;
use App\Models\Payment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

// Handle QR landing flow for dine-in customers
class CustomerController extends Controller
{
    public function showTable(string $code)
    {
        [$table, $session] = $this->resolveActiveSession($code);
        $menus = $this->loadActiveMenus();

        return view('customer.table', [
            'table' => $table,
            'session' => $session,
            'menus' => $menus,
        ]);
    }

    public function checkout(string $code)
    {
        [$table, $session] = $this->resolveActiveSession($code);
        $orders = $this->loadActiveOrders($session, $table);

        $completionMessage = null;

        if ($orders->isEmpty()) {
            $latestPaidOrder = $session->orders()
                ->with('payments')
                ->where('status', Order::STATUS_PAID)
                ->latest('updated_at')
                ->first();

            if ($latestPaidOrder) {
                $completedAt = $latestPaidOrder->updated_at ?? $latestPaidOrder->created_at;

                if ($completedAt && $completedAt->greaterThan(Carbon::now()->subMinutes(10))) {
                    $latestPayment = $latestPaidOrder->payments
                        ->where('status', Payment::STATUS_SUCCESS)
                        ->sortByDesc(function (Payment $payment) {
                            return $payment->paid_at ?? $payment->updated_at ?? $payment->created_at;
                        })
                        ->first();

                    $method = $latestPayment->method ?? null;
                    $completionMessage = $method
                        ? "Pembayaran ($method) telah dikonfirmasi. Pesanan selesai."
                        : 'Pembayaran telah dikonfirmasi. Pesanan selesai.';
                }
            }
        }

        return view('customer.checkout', [
            'table' => $table,
            'session' => $session,
            'orders' => $orders,
            'completionMessage' => $completionMessage,
        ]);
    }

    /**
     * Resolve the active session for the provided table code.
     */
    private function resolveActiveSession(string $code): array
    {
        $table = Table::where('code', $code)->where('is_active', true)->firstOrFail();

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

        return [$table, $session];
    }

    /**
     * Retrieve active menus grouped by category for display.
     */
    private function loadActiveMenus(): Collection
    {
        return Menu::with('options')
            ->where('is_active', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');
    }

    /**
     * Build an order collection suitable for customer status display.
     */
    private function loadActiveOrders(TableSession $session, Table $table): Collection
    {
        $orders = $session->orders()
            ->with('payments')
            ->whereIn('status', [
                Order::STATUS_PLACED,
                Order::STATUS_IN_PROGRESS,
                Order::STATUS_READY,
                Order::STATUS_SERVED,
            ])
            ->orderBy('created_at')
            ->get()
            ->map(function (Order $order) use ($table) {
                $payments = $order->payments->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'method' => $payment->method,
                        'status' => $payment->status,
                        'amount' => (float) $payment->amount,
                        'created_at' => $payment->created_at?->toIso8601String(),
                    ];
                })->values()->all();

                $latestPayment = collect($payments)->sortByDesc('created_at')->first();

                return [
                    'id' => $order->id,
                    'code' => $order->code,
                    'status' => $order->status,
                    'table_code' => $table->code,
                    'created_at' => $order->created_at?->toIso8601String(),
                    'grand_total' => (float) $order->grand_total,
                    'payments' => $payments,
                    'latest_payment_status' => $latestPayment['status'] ?? null,
                    'latest_payment_method' => $latestPayment['method'] ?? null,
                ];
            })
            ->values();

        return $orders;
    }
}
