<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\Table;
use App\Models\TableSession;

// Handle QR landing flow for dine-in customers
class CustomerController extends Controller
{
    public function showTable(string $code)
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

        $menus = Menu::with('options')
            ->where('is_active', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

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

        return view('customer.table', [
            'table' => $table,
            'session' => $session,
            'menus' => $menus,
            'orders' => $orders,
        ]);
    }
}
