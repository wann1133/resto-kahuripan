@extends('layouts.app')

@section('content')
    <div class="space-y-10">
        <header class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-emerald-500">Dashboard</p>
                <h1 class="text-3xl font-semibold text-slate-800">Ringkasan Operasional</h1>
                <p class="text-sm text-slate-500">Monitor performa resto secara real-time dari satu layar.</p>
            </div>
            <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-emerald-600">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                Update per {{ now()->format('H:i') }}
            </span>
        </header>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <x-admin.metric-card title="Meja Aktif" :value="$metrics['active_tables']" trend="Stabil" color="emerald" icon="table" />
            <x-admin.metric-card title="Pesanan Terbuka" :value="$metrics['open_orders']" trend="Diproses" color="sky" icon="receipt" />
            <x-admin.metric-card title="Pendapatan Hari Ini" :value="'Rp '.number_format($metrics['today_revenue'], 0, ',', '.')" trend="+12%" color="violet" icon="cash" />
            <x-admin.metric-card title="Menu Tersedia" :value="$metrics['menu_count'].' menu'" trend="Terverifikasi" color="amber" icon="menu" />
        </section>

        <section class="grid gap-6 lg:grid-cols-[1.6fr_minmax(280px,1fr)]">
            <div class="rounded-3xl border border-slate-200/60 bg-white/90 p-6 shadow-sm shadow-slate-900/5">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-800">Pesanan Terbaru</h2>
                    <span class="text-xs font-medium uppercase tracking-wide text-slate-400">10 entri terakhir</span>
                </div>
                <div class="mt-4 overflow-hidden rounded-2xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-slate-500">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium">Kode</th>
                                <th class="px-4 py-3 text-left font-medium">Meja</th>
                                <th class="px-4 py-3 text-left font-medium">Status</th>
                                <th class="px-4 py-3 text-right font-medium">Total</th>
                                <th class="px-4 py-3 text-right font-medium">Dibayar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white text-slate-700">
                            @foreach ($recentOrders as $order)
                                <tr class="hover:bg-slate-50/70">
                                    <td class="px-4 py-3 font-semibold text-slate-800">{{ $order->code }}</td>
                                    <td class="px-4 py-3">{{ optional($order->tableSession->table)->number ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $order->status }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-slate-800">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right text-slate-500">
                                        {{ optional($order->payments->firstWhere('status', 'SUCCESS'))->paid_at?->format('H:i') ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200/60 bg-white/90 p-6 shadow-sm shadow-slate-900/5">
                <h2 class="text-lg font-semibold text-slate-800">Menu Teratas</h2>
                <p class="mt-1 text-sm text-slate-500">Performa menu favorit pelanggan minggu ini.</p>
                <ul class="mt-4 space-y-3">
                    @foreach ($topMenus as $menu)
                        <li class="flex items-center justify-between rounded-2xl border border-slate-200 px-4 py-3">
                            <div>
                                <p class="text-sm font-semibold text-slate-700">{{ $menu->name }}</p>
                                <p class="text-xs text-slate-400">Kategori {{ $menu->category }}</p>
                            </div>
                            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">{{ $menu->total_sold ?? 0 }} porsi</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    </div>
@endsection
