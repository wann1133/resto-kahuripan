<?php

namespace App\Http\Controllers;

use App\Http\Requests\InitiatePaymentRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Services\OrderService;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

// Simulate QRIS and cashier payment flows
class PaymentController extends Controller
{
    public function initiate(InitiatePaymentRequest $request)
    {
        $order = Order::with('payments')->findOrFail($request->integer('order_id'));
        $method = $request->input('method');

        $payment = $order->payments()
            ->where('status', Payment::STATUS_PENDING)
            ->latest()
            ->first();

        if (! $payment) {
            $payment = $order->payments()->create([
                'method' => $method,
                'amount' => $order->grand_total,
                'status' => Payment::STATUS_PENDING,
            ]);
        } else {
            $payment->update(['method' => $method]);
        }

        if ($method === 'QRIS') {
            $payload = json_encode([
                'order_code' => $order->code,
                'amount' => $order->grand_total,
                'table' => optional($order->tableSession->table)->number,
                'payment_id' => $payment->id,
            ]);

            $svg = QrCode::format('svg')
                ->size(240)
                ->margin(2)
                ->generate($payload);

            return response()->json([
                'message' => 'QRIS berhasil dibuat.',
                'payment_id' => $payment->id,
                'qr_svg' => $svg,
            ]);
        }

        return response()->json([
            'message' => 'Pembayaran akan diproses di kasir.',
            'payment_id' => $payment->id,
        ]);
    }

    public function callback(Request $request, OrderService $orderService)
    {
        $request->validate([
            'payment_id' => ['required', 'integer', 'exists:payments,id'],
            'provider_ref' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:SUCCESS,FAILED'],
        ]);

        $payment = Payment::findOrFail($request->integer('payment_id'));

        if ($request->input('status') === 'SUCCESS') {
            $orderService->markPaymentSuccess($payment, $request->input('provider_ref'));

            return response()->json(['message' => 'Pembayaran sukses.']);
        }

        $payment->update(['status' => Payment::STATUS_FAILED]);

        return response()->json(['message' => 'Pembayaran gagal.'], 422);
    }

    public function markAsPaid(Payment $payment, OrderService $orderService)
    {
        $orderService->markPaymentSuccess($payment, 'CASHIER-'.now()->timestamp);

        return redirect()->back()->with('success', 'Pembayaran berhasil ditandai lunas.');
    }
}
