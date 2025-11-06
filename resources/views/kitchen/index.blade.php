@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <header class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-emerald-500">Kitchen Display</p>
                <h2 class="text-2xl font-semibold text-slate-800">Antrean Pesanan</h2>
                <p class="text-sm text-slate-500">Kelola status produksi agar ritme dapur tetap stabil.</p>
            </div>
            <span class="inline-flex items-center gap-2 rounded-full bg-emerald-100/70 px-4 py-1.5 text-xs font-semibold uppercase tracking-wide text-emerald-700">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                Realtime aktif
            </span>
        </header>

        <div id="kitchen-board" class="grid gap-4 lg:grid-cols-2">
            @forelse ($orders as $order)
                <article class="order-card rounded-3xl border border-slate-200/60 bg-white/90 p-6 shadow-sm shadow-slate-900/5" data-order-id="{{ $order->id }}">
                    <header class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-slate-800">{{ $order->code }}</p>
                            <p class="text-xs text-slate-400">Meja {{ optional($order->tableSession->table)->number ?? '-' }}</p>
                            <p class="mt-2 text-xs text-slate-400">Dibuat {{ $order->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="status-badge rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $order->status }}</span>
                    </header>

                    <ul class="mt-4 space-y-3 text-sm text-slate-600">
                        @foreach ($order->items as $item)
                            <li class="flex items-center justify-between rounded-2xl border border-slate-200 px-3 py-2">
                                <span class="font-medium text-slate-700">{{ $item->menu->name }} <span class="text-xs text-slate-400">× {{ $item->qty }}</span></span>
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $item->status }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <footer class="mt-5 flex flex-wrap gap-2 text-sm">
                        <form method="POST" action="{{ route('kitchen.update', $order) }}">
                            @csrf
                            <input type="hidden" name="status" value="IN_PROGRESS">
                            <button class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-4 py-2 font-semibold text-amber-700 transition hover:border-amber-300 hover:bg-amber-100">
                                Proses
                            </button>
                        </form>
                        <form method="POST" action="{{ route('kitchen.update', $order) }}">
                            @csrf
                            <input type="hidden" name="status" value="READY">
                            <button class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 font-semibold text-emerald-700 transition hover:border-emerald-300 hover:bg-emerald-100">
                                Siap Disajikan
                            </button>
                        </form>
                        <form method="POST" action="{{ route('kitchen.update', $order) }}">
                            @csrf
                            <input type="hidden" name="status" value="SERVED">
                            <button class="inline-flex items-center gap-2 rounded-full border border-sky-200 bg-sky-50 px-4 py-2 font-semibold text-sky-700 transition hover:border-sky-300 hover:bg-sky-100">
                                Sudah Disajikan
                            </button>
                        </form>
                    </footer>
                </article>
            @empty
                <p class="rounded-3xl border border-dashed border-slate-200 bg-white/80 p-10 text-center text-sm text-slate-500">Belum ada pesanan baru. Antrean akan tampil otomatis saat pelanggan mengirim order.</p>
            @endforelse
        </div>
    </div>

    <script>
        const board = document.getElementById('kitchen-board');
        const activeStatuses = ['PLACED', 'IN_PROGRESS', 'READY'];

        function buildItemRow(item) {
            return `
                <li class="flex items-center justify-between rounded-2xl border border-slate-200 px-3 py-2">
                    <span class="font-medium text-slate-700">${item.menu ?? ''} <span class="text-xs text-slate-400">× ${item.qty}</span></span>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-500">${item.status ?? '-'}</span>
                </li>
            `;
        }

        function renderCard(order) {
            const shouldShow = activeStatuses.includes(order.status);
            let card = board.querySelector(`[data-order-id="${order.id}"]`);

            if (! shouldShow) {
                if (card) {
                    card.remove();
                }
                return;
            }

            if (! card) {
                card = document.createElement('article');
                card.className = 'order-card rounded-3xl border border-slate-200/60 bg-white/90 p-6 shadow-sm shadow-slate-900/5';
                card.dataset.orderId = order.id;
                board.prepend(card);
            }

            const itemsHtml = (order.items || []).map(buildItemRow).join('');

            card.innerHTML = `
                <header class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-800">${order.code}</p>
                        <p class="text-xs text-slate-400">Meja ${order.table ?? '-'}</p>
                    </div>
                    <span class="status-badge rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-500">${order.status}</span>
                </header>
                <ul class="mt-4 space-y-3 text-sm text-slate-600">${itemsHtml}</ul>
                <footer class="mt-5 flex flex-wrap gap-2 text-sm">
                    <form method="POST" action="/kitchen/order/${order.id}/update-status">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="status" value="IN_PROGRESS">
                        <button class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-4 py-2 font-semibold text-amber-700 transition hover:border-amber-300 hover:bg-amber-100">Proses</button>
                    </form>
                    <form method="POST" action="/kitchen/order/${order.id}/update-status">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="status" value="READY">
                        <button class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 font-semibold text-emerald-700 transition hover:border-emerald-300 hover:bg-emerald-100">Siap Disajikan</button>
                    </form>
                    <form method="POST" action="/kitchen/order/${order.id}/update-status">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="status" value="SERVED">
                        <button class="inline-flex items-center gap-2 rounded-full border border-sky-200 bg-sky-50 px-4 py-2 font-semibold text-sky-700 transition hover:border-sky-300 hover:bg-sky-100">Sudah Disajikan</button>
                    </form>
                </footer>
            `;
        }
                return;
            }

            card.innerHTML = `
                <header class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-800">${order.code}</p>
                        <p class="text-xs text-slate-400">Meja ${order.table ?? '-'}</p>
                    </div>
                    <span class="status-badge rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-500">${order.status}</span>
                </header>
                <ul class="mt-4 space-y-3 text-sm text-slate-600">${itemsHtml}</ul>
                <footer class="mt-5 flex flex-wrap gap-2 text-sm">
                    <form method="POST" action="/kitchen/order/${order.id}/update-status">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="status" value="IN_PROGRESS">
                        <button class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-4 py-2 font-semibold text-amber-700 transition hover:border-amber-300 hover:bg-amber-100">Proses</button>
                    </form>
                    <form method="POST" action="/kitchen/order/${order.id}/update-status">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="status" value="READY">
                        <button class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 font-semibold text-emerald-700 transition hover:border-emerald-300 hover:bg-emerald-100">Siap Disajikan</button>
                    </form>
                    <form method="POST" action="/kitchen/order/${order.id}/update-status">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="status" value="SERVED">
                        <button class="inline-flex items-center gap-2 rounded-full border border-sky-200 bg-sky-50 px-4 py-2 font-semibold text-sky-700 transition hover:border-sky-300 hover:bg-sky-100">Sudah Disajikan</button>
                    </form>
                </footer>
            `;
        }

        if (window.Echo) {
            window.Echo.channel('orders').listen('.order.updated', (event) => {
                renderCard(event);
            });
        }
    </script>
@endsection
