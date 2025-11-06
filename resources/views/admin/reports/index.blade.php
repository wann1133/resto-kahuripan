@extends('layouts.app')

@section('content')
    <div class="space-y-8">
        <form method="GET" class="rounded-3xl border border-slate-200/60 bg-white/90 p-6 shadow-sm shadow-slate-900/5 text-sm text-slate-600">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.35em] text-emerald-500">Laporan Penjualan</p>
                    <h2 class="text-xl font-semibold text-slate-800">Ringkasan Transaksi</h2>
                </div>
                <div class="rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-emerald-600">
                    Total Pendapatan · Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </div>
            </div>
            <div class="mt-6 grid gap-4 md:grid-cols-5">
                <label class="space-y-2 md:col-span-2">
                    <span class="font-semibold text-slate-700">Mulai</span>
                    <input type="date" name="start_date" value="{{ $start->format('Y-m-d') }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-100">
                </label>
                <label class="space-y-2 md:col-span-2">
                    <span class="font-semibold text-slate-700">Sampai</span>
                    <input type="date" name="end_date" value="{{ $end->format('Y-m-d') }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-100">
                </label>
                <div class="flex items-end">
                    <button class="inline-flex w-full items-center justify-center rounded-full bg-emerald-500 px-4 py-3 font-semibold text-white shadow-sm shadow-emerald-500/40 transition hover:bg-emerald-600">
                        Terapkan
                    </button>
                </div>
            </div>
        </form>

        <section class="rounded-3xl border border-slate-200/60 bg-white/90 p-6 shadow-sm shadow-slate-900/5">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-800">Detail Pesanan</h2>
                <span class="text-xs font-medium uppercase tracking-wide text-slate-400">{{ $orders->count() }} entri</span>
            </div>
            <div class="mt-4 overflow-hidden rounded-2xl border border-slate-200">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-slate-500">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium">Tanggal</th>
                            <th class="px-4 py-3 text-left font-medium">Kode</th>
                            <th class="px-4 py-3 text-left font-medium">Meja</th>
                            <th class="px-4 py-3 text-left font-medium">Status</th>
                            <th class="px-4 py-3 text-right font-medium">Total</th>
                            <th class="px-4 py-3 text-right font-medium">Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white text-slate-700">
                        @forelse ($orders as $order)
                            @php
                                $successPayment = $order->payments->firstWhere('status', 'SUCCESS');
                            @endphp
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-4 py-3 text-slate-500">{{ $order->created_at->format('d M Y · H:i') }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $order->code }}</td>
                                <td class="px-4 py-3">{{ optional($order->tableSession->table)->number ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $order->status }}</span>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-slate-800">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-slate-500">
                                    {{ $successPayment ? $successPayment->method.' · '.optional($successPayment->paid_at)->format('d/m H:i') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-5 text-center text-slate-400">Tidak ada data untuk rentang tanggal ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
