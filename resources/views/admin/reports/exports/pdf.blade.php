<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <title>Laporan Penjualan</title>
        <style>
            @page { margin: 24px 28px; }
            body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #1e293b; }
            h1 { font-size: 20px; margin-bottom: 4px; }
            h2 { font-size: 14px; margin: 0; }
            .meta { margin-bottom: 16px; }
            .meta p { margin: 2px 0; }
            table { width: 100%; border-collapse: collapse; margin-top: 12px; }
            th, td { border: 1px solid #CBD5E1; padding: 8px; }
            th { background: #0f172a; color: #fff; font-weight: 600; text-align: left; }
            td { vertical-align: top; }
            .text-right { text-align: right; }
            .muted { color: #64748b; }
            .summary { background: #f1f5f9; border-radius: 12px; padding: 12px 16px; margin-top: 12px; }
            .summary span { display: inline-block; margin-right: 16px; font-weight: 600; }
        </style>
    </head>
    <body>
        <header>
            <h1>Laporan Penjualan</h1>
            <div class="meta">
                <p>Periode: {{ $start->translatedFormat('d F Y') }} &ndash; {{ $end->translatedFormat('d F Y') }}</p>
                <p>Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}</p>
            </div>
            <div class="summary">
                <span>Total pendapatan: Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                <span>Total pesanan: {{ $orders->count() }} transaksi</span>
            </div>
        </header>

        <table>
            <thead>
                <tr>
                    <th style="width: 16%;">Tanggal</th>
                    <th style="width: 14%;">Kode Pesanan</th>
                    <th style="width: 12%;">Meja</th>
                    <th style="width: 16%;">Status</th>
                    <th style="width: 14%;" class="text-right">Total</th>
                    <th style="width: 28%;">Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    @php
                        $successPayment = $order->payments->firstWhere('status', 'SUCCESS');
                    @endphp
                    <tr>
                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $order->code }}</td>
                        <td>{{ optional($order->tableSession->table)->number ?? '-' }}</td>
                        <td>{{ $order->status }}</td>
                        <td class="text-right">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                        <td>
                            @if ($successPayment)
                                <p style="margin:0;">Metode: {{ $successPayment->method }}</p>
                                @if ($successPayment->paid_at)
                                    <p style="margin:0;" class="muted">Dibayar: {{ $successPayment->paid_at->format('d/m H:i') }}</p>
                                @endif
                            @else
                                <span class="muted">Belum ada pembayaran selesai</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="muted" style="text-align: center;">Tidak ada data untuk periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </body>
</html>
