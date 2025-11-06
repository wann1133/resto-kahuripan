@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <header>
            <p class="text-xs uppercase tracking-[0.35em] text-emerald-500">Pesanan</p>
            <h2 class="text-2xl font-semibold text-slate-800">Ringkasan Meja</h2>
            <p class="text-sm text-slate-500">Lihat riwayat item yang sudah dikirim ke dapur beserta status terbarunya.</p>
        </header>

        <div class="space-y-4">
            @forelse ($orders ?? [] as $order)
                <article class="rounded-3xl border border-slate-200/60 bg-white/90 p-6 shadow-sm shadow-slate-900/5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-slate-800">{{ $order->code }}</p>
                            <p class="text-xs text-slate-400">Dipesan {{ $order->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $order->status }}</span>
                    </div>
                    <ul class="mt-4 space-y-3 text-sm text-slate-600">
                        @foreach ($order->items as $item)
                            <li class="flex items-center justify-between rounded-2xl border border-slate-200 px-4 py-3">
                                <span class="font-medium text-slate-700">{{ $item->menu->name }} <span class="text-xs text-slate-400">× {{ $item->qty }}</span></span>
                                <span class="font-semibold text-slate-800">Rp {{ number_format($item->price * $item->qty, 0, ',', '.') }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-4 rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-500">
                        Catatan pelanggan: {{ $order->notes ?: '-' }}
                    </div>
                </article>
            @empty
                <p class="rounded-3xl border border-dashed border-slate-200 bg-white/80 p-10 text-center text-sm text-slate-500">Belum ada pesanan yang tercatat untuk meja ini.</p>
            @endforelse
        </div>
    </div>
@endsection
