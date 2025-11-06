<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Services\OrderService;

// Manage customer orders
class OrderController extends Controller
{
    public function store(StoreOrderRequest $request, OrderService $orderService)
    {
        $data = $request->validated();

        try {
            $order = $orderService->createOrder(
                $data['table_code'],
                $data['items'],
                $data['notes'] ?? null,
                $data['payment']['method']
            );
        } catch (\RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Pesanan berhasil dibuat.',
            'order' => $order,
        ], 201);
    }

    public function status(Order $order)
    {
        return response()->json([
            'id' => $order->id,
            'code' => $order->code,
            'status' => $order->status,
            'session_id' => $order->table_session_id,
            'items' => $order->items()->with('menu')->get(),
            'payments' => $order->payments,
        ]);
    }
}
