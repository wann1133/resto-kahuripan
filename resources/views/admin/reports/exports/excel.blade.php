<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <title>Laporan Penjualan</title>
    </head>
    <body>
        <table border="1" cellpadding="6" cellspacing="0" width="100%">
            <thead>
                <tr style="background-color:#E2F5EC;font-weight:bold;">
                    <th colspan="6" style="text-align:left;">Laporan Penjualan</th>
                </tr>
                <tr>
                    <th colspan="6" style="text-align:left;">
                        Periode: {{ $start->translatedFormat('d F Y') }} &ndash; {{ $end->translatedFormat('d F Y') }}
                    </th>
                </tr>
                <tr>
                    <th colspan="6" style="text-align:left;">
                        Total Pendapatan: Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                    </th>
                </tr>
                <tr style="background-color:#F1F5F9;">
                    <th style="text-align:left;">Tanggal</th>
                    <th style="text-align:left;">Kode Pesanan</th>
                    <th style="text-align:left;">Meja</th>
                    <th style="text-align:left;">Status</th>
                    <th style="text-align:right;">Total</th>
                    <th style="text-align:left;">Pembayaran</th>
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
                        <td style="text-align:right;">{{ number_format($order->grand_total, 0, ',', '.') }}</td>
                        <td>
                            @if ($successPayment)
                                {{ $successPayment->method }} {{ $successPayment->paid_at ? '('.$successPayment->paid_at->format('d/m H:i').')' : '' }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;">Tidak ada data untuk periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </body>
</html>
