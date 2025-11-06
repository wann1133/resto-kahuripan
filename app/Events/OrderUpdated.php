<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// Push order changes to Echo/Pusher for kitchen screen
class OrderUpdated implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public Order $order)
    {
    }

    public function broadcastOn(): Channel
    {
        return new Channel('orders');
    }

    public function broadcastWith(): array
    {
        $latestPayment = $this->order->payments->sortByDesc('created_at')->first();

        return [
            'id' => $this->order->id,
            'code' => $this->order->code,
            'status' => $this->order->status,
            'table' => $this->order->tableSession->table->number ?? null,
            'table_code' => $this->order->tableSession->table->code ?? null,
            'created_at' => $this->order->created_at?->toIso8601String(),
            'grand_total' => (float) $this->order->grand_total,
            'items' => $this->order->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'menu' => $item->menu->name ?? '',
                    'qty' => $item->qty,
                    'status' => $item->status,
                ];
            })->values()->all(),
            'payments' => $this->order->payments->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'method' => $payment->method,
                    'status' => $payment->status,
                    'amount' => (float) $payment->amount,
                    'created_at' => $payment->created_at?->toIso8601String(),
                ];
            })->values()->all(),
            'latest_payment_status' => $latestPayment->status ?? null,
            'latest_payment_method' => $latestPayment->method ?? null,
        ];
    }

    public function broadcastAs(): string
    {
        return 'order.updated';
    }
}
