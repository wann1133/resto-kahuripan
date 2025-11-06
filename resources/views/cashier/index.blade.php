@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <header class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-emerald-500">Kasir</p>
                <h2 class="text-2xl font-semibold text-slate-800">Pembayaran Menunggu</h2>
                <p class="text-sm text-slate-500">Selesaikan transaksi pelanggan dan tutup sesi meja.</p>
            </div>
            <span class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-amber-600">
                {{ $payments->count() }} pembayaran aktif
            </span>
        </header>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($payments as $payment)
                <article class="rounded-3xl border border-slate-200/60 bg-white/90 p-6 shadow-sm shadow-slate-900/5">
                    <div class="flex items-start justify-between gap-4 text-sm text-slate-600">
                        <div>
                            <p class="text-sm font-semibold text-slate-800">{{ $payment->order->code }}</p>
                            <p class="text-xs text-slate-400">Meja {{ optional($payment->order->tableSession->table)->number ?? '-' }}</p>
                            <p class="mt-1 text-xs text-slate-400">Dibuat {{ $payment->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-amber-600">{{ $payment->method }}</span>
                    </div>
                    <p class="mt-5 text-2xl font-semibold text-slate-800">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                    <form action="{{ route('cashier.payments.markPaid', $payment) }}" method="POST" class="mt-6">
                        @csrf
                        <button class="inline-flex w-full items-center justify-center rounded-full bg-emerald-500 px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-emerald-500/40 transition hover:bg-emerald-600">
                            Tandai Lunas
                        </button>
                    </form>
                </article>
            @empty
                <p class="rounded-3xl border border-dashed border-slate-200 bg-white/80 p-10 text-center text-sm text-slate-500 md:col-span-2 xl:col-span-3">
                    Tidak ada pembayaran menunggu.
                </p>
            @endforelse
        </div>
    </div>
@endsection
