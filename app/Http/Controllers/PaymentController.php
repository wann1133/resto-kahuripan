<?php

namespace App\Http\Controllers;

use App\Http\Requests\InitiatePaymentRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Setting;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
            $payload = $this->buildDynamicQrisPayload((float) $order->grand_total);
            $svgPayload = $payload ?? $this->fallbackPayload($order, $payment);

            $svg = QrCode::format('svg')
                ->size(240)
                ->margin(2)
                ->generate($svgPayload);
            $svgDataUri = 'data:image/svg+xml;base64,' . base64_encode($svg);

            return response()->json([
                'message' => $payload ? 'QRIS dinamis berhasil dibuat.' : 'QRIS fallback berhasil dibuat.',
                'payment_id' => $payment->id,
                'amount' => (float) $order->grand_total,
                'qr_svg' => $svg,
                'qr_svg_data_url' => $svgDataUri,
                'qr_payload' => $payload,
                'static_image_url' => $this->qrisImageUrl(),
                'is_dynamic' => (bool) $payload,
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

    private function buildDynamicQrisPayload(float $amount): ?string
    {
        $basePayload = Setting::value('payments.qris_payload');

        if (! $basePayload) {
            return null;
        }

        $normalized = strtoupper(trim(preg_replace('/\s+/', '', $basePayload)));

        if (! Str::startsWith($normalized, '000')) {
            return null;
        }

        $crcPosition = strrpos($normalized, '6304');

        if ($crcPosition === false) {
            return null;
        }

        $payload = substr($normalized, 0, $crcPosition);
        $payload = $this->removeTag($payload, '54');

        $amountString = number_format($amount, 2, '.', '');
        $payload .= '54'.str_pad(strlen($amountString), 2, '0', STR_PAD_LEFT).$amountString;
        $payload .= '6304';

        $crc = $this->calculateCrc16($payload);

        return $payload.$crc;
    }

    private function removeTag(string $payload, string $tag): string
    {
        $index = strpos($payload, $tag);

        if ($index === false) {
            return $payload;
        }

        $lengthFragment = substr($payload, $index + 2, 2);

        if (! ctype_digit($lengthFragment)) {
            return $payload;
        }

        $length = (int) $lengthFragment;

        return substr($payload, 0, $index).substr($payload, $index + 4 + $length);
    }

    private function calculateCrc16(string $payload): string
    {
        $crc = 0xFFFF;
        $polynomial = 0x1021;

        $bytes = array_map('ord', str_split($payload));

        foreach ($bytes as $byte) {
            $crc ^= ($byte << 8);

            for ($i = 0; $i < 8; $i++) {
                if ($crc & 0x8000) {
                    $crc = (($crc << 1) ^ $polynomial) & 0xFFFF;
                } else {
                    $crc = ($crc << 1) & 0xFFFF;
                }
            }
        }

        return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
    }

    private function fallbackPayload(Order $order, Payment $payment): string
    {
        return json_encode([
            'order_code' => $order->code,
            'amount' => $order->grand_total,
            'table' => optional($order->tableSession->table)->number,
            'payment_id' => $payment->id,
        ]);
    }

    private function qrisImageUrl(): ?string
    {
        $path = Setting::value('payments.qris_image_path');

        if (! $path) {
            return null;
        }

        return Storage::disk('public')->exists($path)
            ? Storage::disk('public')->url($path)
            : null;
    }
}
