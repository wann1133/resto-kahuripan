<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Services\OrderService;

// Kitchen Display System endpoints
class KitchenController extends Controller
{
    public function index()
    {
        $orders = Order::with(['items.menu', 'tableSession.table'])
            ->whereIn('status', [Order::STATUS_PLACED, Order::STATUS_IN_PROGRESS, Order::STATUS_READY])
            ->orderByRaw("FIELD(status, 'PLACED','IN_PROGRESS','READY')")
            ->orderBy('created_at')
            ->get();

        return view('kitchen.index', ['orders' => $orders]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order, OrderService $orderService)
    {
        $status = $request->validated()['status'];

        $orderService->updateOrderStatus($order, $status);

        if (in_array($status, [Order::STATUS_READY, Order::STATUS_IN_PROGRESS, Order::STATUS_SERVED], true)) {
            $order->items()->update(['status' => $status]);
        }

        return back()->with('success', 'Status pesanan diperbarui.');
    }
}
