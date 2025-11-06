@extends('layouts.app')

@section('content')
    <div class="grid gap-8 lg:grid-cols-[1.7fr_minmax(320px,1fr)]">
        <section class="space-y-6">
            <div class="rounded-3xl bg-gradient-to-r from-emerald-500 via-emerald-400 to-teal-400 px-8 py-10 text-white shadow-xl shadow-emerald-500/30">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-sm uppercase tracking-[0.35em] text-white/70">Sesi Meja</p>
                        <h1 class="mt-2 text-3xl font-semibold">Meja {{ $table->number }}</h1>
                        <p class="mt-2 text-sm text-white/80">Kode QR: <span class="font-medium text-white">{{ $table->code }}</span></p>
                        <p class="mt-3 text-sm text-white/80">Mulai {{ $session->started_at->translatedFormat('l, d F Y · H:i') }}</p>
                    </div>
                    <div class="flex flex-col items-start gap-3 md:items-end">
                        <span class="inline-flex rounded-full bg-white/15 px-4 py-1 text-xs font-semibold uppercase tracking-wide text-white">{{ $session->status }}</span>
                        <a href="{{ url()->current() }}" class="inline-flex items-center gap-2 rounded-full border border-white/40 bg-white/10 px-4 py-2 text-sm font-medium text-white/90 transition hover:bg-white/20">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 5v6h-6" />
                                <path d="M3 19v-6h6" />
                                <path d="M21 5c-1.5-1.5-3.79-2-6-2-5.52 0-10 4.48-10 10" />
                                <path d="M3 19c1.5 1.5 3.79 2 6 2 5.52 0 10-4.48 10-10" />
                            </svg>
                            Muat ulang menu
                        </a>
                    </div>
                </div>
            </div>

            @foreach ($menus as $category => $items)
                <section class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-800">{{ $category }}</h2>
                            <p class="text-sm text-slate-500">{{ $items->count() }} pilihan tersedia</p>
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ($items as $menu)
                            @php
                                $menuPayload = [
                                    'id' => $menu->id,
                                    'name' => $menu->name,
                                    'price' => $menu->price,
                                    'options' => $menu->options->map(function ($option) {
                                        return [
                                            'id' => $option->id,
                                            'name' => $option->name,
                                            'extra_price' => $option->extra_price,
                                        ];
                                    })->values()->all(),
                                ];
                            @endphp
                            <article class="group flex h-full flex-col justify-between rounded-3xl border border-slate-200/60 bg-white/90 p-6 shadow-sm shadow-slate-900/5 transition hover:-translate-y-1 hover:border-emerald-300 hover:shadow-lg">
                                <div class="space-y-3">
                                    <div class="inline-flex items-center gap-2 text-xs font-medium uppercase tracking-wide text-emerald-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Favorit Resto
                                    </div>
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <h3 class="text-base font-semibold text-slate-800">{{ $menu->name }}</h3>
                                            <p class="mt-2 text-sm leading-relaxed text-slate-500">{{ $menu->description ?? 'Menu spesial pilihan chef kami.' }}</p>
                                        </div>
                                        <span class="text-right text-lg font-semibold text-emerald-600">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                                    </div>

                                    @if ($menu->options->isNotEmpty())
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            @foreach ($menu->options as $option)
                                                <span class="rounded-full border border-slate-200 px-3 py-1 text-xs font-medium text-slate-500">{{ $option->name }}@if ($option->extra_price)
                                                        <span class="text-emerald-600"> +Rp {{ number_format($option->extra_price, 0, ',', '.') }}</span>
                                                    @endif
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <button
                                    data-menu='@json($menuPayload)'
                                    class="mt-6 inline-flex items-center justify-center rounded-full bg-emerald-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-600">
                                    Tambah ke Keranjang
                                </button>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </section>

        <aside class="space-y-6">
            <div class="rounded-3xl border border-slate-200/60 bg-white/90 p-6 shadow-sm shadow-slate-900/5">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-slate-800">Keranjang</h3>
                    <button id="clear-cart" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">Kosongkan</button>
                </div>
                <p class="mt-1 text-sm text-slate-500">Periksa kembali pesanan Anda sebelum dikirim ke dapur.</p>

                <div id="cart-empty" class="mt-6 rounded-2xl border border-dashed border-slate-200 px-4 py-6 text-center text-sm text-slate-500">
                    Belum ada item di keranjang.
                </div>
                <div id="cart-items" class="mt-4 hidden space-y-4 text-sm"></div>

                <div class="mt-6 space-y-2 rounded-2xl bg-slate-50 px-4 py-4 text-sm">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal</span>
                        <span id="subtotal">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-slate-500">
                        <span>Estimasi Pajak (10%)</span>
                        <span id="tax">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-slate-500">
                        <span>Service (5%)</span>
                        <span id="service">Rp 0</span>
                    </div>
                    <div class="flex justify-between border-t border-slate-200 pt-3 text-base font-semibold text-slate-800">
                        <span>Total Pembayaran</span>
                        <span id="grand-total">Rp 0</span>
                    </div>
                </div>

                <form id="order-form" class="mt-6 space-y-4">
                    @csrf
                    <label class="block text-sm font-medium text-slate-600">
                        Catatan
                        <textarea name="notes" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-inner focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-100" rows="3" placeholder="Contoh: kurang pedas, sambal pisah"></textarea>
                    </label>
                    <label class="block text-sm font-medium text-slate-600">
                        Metode Pembayaran
                        <select name="payment_method" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-100">
                            <option value="CASH">Bayar di Kasir</option>
                            <option value="QRIS">QRIS</option>
                        </select>
                    </label>
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-emerald-500 px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-emerald-500/40 transition hover:bg-emerald-600 disabled:cursor-not-allowed disabled:bg-slate-300" disabled>
                        Kirim Pesanan ke Dapur
                    </button>
                </form>
                <div id="order-feedback" class="mt-3 text-sm text-slate-500"></div>
            </div>

            <div class="rounded-3xl border border-slate-200/60 bg-white/90 p-6 shadow-sm shadow-slate-900/5">
                <h3 class="text-lg font-semibold text-slate-800">Status Pesanan</h3>
                <p class="mt-1 text-sm text-slate-500">Pantau progres pesanan yang sudah dikirim.</p>
                <ul id="order-status-list" class="mt-4 space-y-3 text-sm text-slate-600">
                    <li>Belum ada pesanan.</li>
                </ul>
            </div>
        </aside>
    </div>

    <script>
        const tableCode = @json($table->code);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const cart = [];
        const rates = { tax: 0.1, service: 0.05 };

        const cartEmpty = document.getElementById('cart-empty');
        const cartItemsContainer = document.getElementById('cart-items');
        const subtotalEl = document.getElementById('subtotal');
        const taxEl = document.getElementById('tax');
        const serviceEl = document.getElementById('service');
        const grandTotalEl = document.getElementById('grand-total');
        const orderForm = document.getElementById('order-form');
        const submitBtn = orderForm.querySelector('button[type="submit"]');
        const feedbackEl = document.getElementById('order-feedback');
        const statusList = document.getElementById('order-status-list');
        const orderStatuses = new Map();
        const clearCartBtn = document.getElementById('clear-cart');
        const initialOrders = @json($orders);
        const statusFlow = [
            { key: 'PLACED', label: 'Pesanan diterima' },
            { key: 'IN_PROGRESS', label: 'Sedang diproses di dapur' },
            { key: 'READY', label: 'Siap disajikan' },
            { key: 'SERVED', label: 'Sudah disajikan' },
            { key: 'PAID', label: 'Pembayaran selesai' },
        ];
        const statusPriority = statusFlow.reduce((map, step, index) => {
            map[step.key] = index;
            return map;
        }, {});
        const statusDisplayMap = {
            PLACED: 'Menunggu diproses',
            IN_PROGRESS: 'Sedang diproses',
            READY: 'Siap disajikan',
            SERVED: 'Sudah disajikan',
            PAID: 'Pembayaran selesai',
        };
        const finalOrderStatuses = new Set(['PAID', 'CLOSED']);

        function formatCurrency(value) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value);
        }

        function renderCart() {
            if (cart.length === 0) {
                cartEmpty.classList.remove('hidden');
                cartItemsContainer.classList.add('hidden');
                submitBtn.disabled = true;
            } else {
                cartEmpty.classList.add('hidden');
                cartItemsContainer.classList.remove('hidden');
                submitBtn.disabled = false;
            }

            cartItemsContainer.innerHTML = '';

            let subtotal = 0;

            cart.forEach((item, index) => {
                const linePrice = (item.price + item.optionTotal) * item.qty;
                subtotal += linePrice;
                const wrapper = document.createElement('div');
                wrapper.className = 'rounded-2xl border border-slate-200 px-4 py-3';
                wrapper.innerHTML = `
                    <div class="flex items-start justify-between gap-3">
                        <div class="space-y-1">
                            <p class="font-semibold text-slate-700">${item.name}</p>
                            <p class="text-xs text-slate-500">${formatCurrency(item.price)} · ${item.qty} porsi</p>
                            ${item.notes ? `<p class="text-xs text-slate-400">Catatan: ${item.notes}</p>` : ''}
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-slate-700">${formatCurrency(linePrice)}</p>
                            <button data-index="${index}" class="mt-2 text-xs font-semibold text-rose-500 hover:text-rose-600">Hapus</button>
                        </div>
                    </div>
                `;
                cartItemsContainer.appendChild(wrapper);
            });

            const tax = subtotal * rates.tax;
            const service = subtotal * rates.service;
            const grand = subtotal + tax + service;

            subtotalEl.textContent = formatCurrency(subtotal);
            taxEl.textContent = formatCurrency(tax);
            serviceEl.textContent = formatCurrency(service);
            grandTotalEl.textContent = formatCurrency(grand);
        }

        document.querySelectorAll('[data-menu]').forEach((button) => {
            button.addEventListener('click', () => {
                const data = JSON.parse(button.dataset.menu);
                const qty = 1;
                cart.push({
                    menu_id: data.id,
                    name: data.name,
                    price: Number(data.price),
                    options: [],
                    optionTotal: 0,
                    qty,
                    notes: '',
                });
                renderCart();
            });
        });

        if (clearCartBtn) {
            clearCartBtn.addEventListener('click', () => {
                cart.length = 0;
                renderCart();
            });
        }

        cartItemsContainer.addEventListener('click', (event) => {
            if (event.target.matches('button[data-index]')) {
                const index = Number(event.target.dataset.index);
                cart.splice(index, 1);
                renderCart();
            }
        });

        orderForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            submitBtn.disabled = true;
            feedbackEl.textContent = 'Mengirim pesanan...';

            const formData = new FormData(orderForm);
            const payload = {
                table_code: tableCode,
                notes: formData.get('notes'),
                items: cart.map((item) => ({
                    menu_id: item.menu_id,
                    qty: item.qty,
                    options: item.options,
                    notes: item.notes,
                })),
                payment: {
                    method: formData.get('payment_method'),
                },
            };

            const response = await fetch('{{ route('order.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(payload),
            });

            if (response.ok) {
                const data = await response.json();
                cart.length = 0;
                renderCart();
                data.order.table_code = tableCode;
                data.order.created_at = data.order.created_at ?? new Date().toISOString();
                feedbackEl.textContent = data.message;
                appendStatus(data.order);
            } else {
                const error = await response.json().catch(() => ({ message: 'Terjadi kesalahan.' }));
                feedbackEl.textContent = error.message || 'Terjadi kesalahan saat mengirim pesanan.';
            }

            submitBtn.disabled = cart.length === 0;
        });

        function appendStatus(order) {
            if (order.table_code && order.table_code !== tableCode) {
                return;
            }

            if (! isOrderActive(order.status)) {
                orderStatuses.delete(order.id);
                renderStatusList();
                return;
            }

            const current = orderStatuses.get(order.id) || {};
            const merged = enrichOrderData(current, order);

            orderStatuses.set(merged.id, merged);

            renderStatusList();
        }

        function isOrderActive(status) {
            return ! finalOrderStatuses.has(status);
        }

        function enrichOrderData(existing, incoming) {
            const mergedPayments = Array.isArray(incoming.payments) && incoming.payments.length > 0
                ? incoming.payments
                : existing.payments || [];

            const merged = {
                ...existing,
                ...incoming,
                payments: mergedPayments,
            };

            if (! merged.table_code) {
                merged.table_code = tableCode;
            }

            merged.created_at = merged.created_at || existing.created_at || null;

            const latestPayment = determineLatestPayment(merged);

            merged.latest_payment_status = incoming.latest_payment_status
                ?? existing.latest_payment_status
                ?? latestPayment?.status
                ?? null;

            merged.latest_payment_method = incoming.latest_payment_method
                ?? existing.latest_payment_method
                ?? latestPayment?.method
                ?? null;

            if (merged.status === 'PAID' && merged.latest_payment_status !== 'SUCCESS') {
                merged.latest_payment_status = 'SUCCESS';
            }

            return merged;
        }

        function determineLatestPayment(order) {
            const payments = Array.isArray(order.payments) ? order.payments.slice() : [];

            if (payments.length === 0) {
                return null;
            }

            return payments.sort((a, b) => {
                return new Date(b.created_at || 0) - new Date(a.created_at || 0);
            })[0];
        }

        function renderStatusList() {
            statusList.innerHTML = '';

            if (orderStatuses.size === 0) {
                const emptyState = document.createElement('li');
                emptyState.textContent = 'Belum ada pesanan.';
                statusList.appendChild(emptyState);
                return;
            }

            const sortedOrders = Array.from(orderStatuses.values()).sort((a, b) => {
                return new Date(b.created_at || 0) - new Date(a.created_at || 0);
            });

            sortedOrders.forEach((order) => {
                statusList.appendChild(buildStatusCard(order));
            });
        }

        function buildStatusCard(order) {
            const item = document.createElement('li');
            item.className = 'space-y-3 rounded-2xl border border-slate-200 px-4 py-4';

            const statusLabel = humanizeStatus(order.status);
            const statusIndex = resolveStatusIndex(order.status);

            item.innerHTML = `
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-slate-700">${order.code}</p>
                        ${order.created_at ? `<p class="text-xs text-slate-400">Dibuat ${formatShortDate(order.created_at)}</p>` : ''}
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-500">${statusLabel}</span>
                </div>
                <div class="space-y-2">
                    ${renderStatusSteps(order, statusIndex)}
                </div>
                ${buildPaymentWarning(order)}
            `;

            return item;
        }

        function renderStatusSteps(order, statusIndex) {
            return statusFlow.map((step, index) => {
                const reached = isStepReached(order, step, index, statusIndex);
                const indicatorClass = reached ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-400';
                const textClass = reached ? 'text-slate-700' : 'text-slate-400';
                const indicatorText = reached ? '&#10003;' : index + 1;

                return `
                    <div class="flex items-center gap-3">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full text-xs font-semibold ${indicatorClass}">
                            ${indicatorText}
                        </span>
                        <span class="text-xs font-medium ${textClass}">${step.label}</span>
                    </div>
                `;
            }).join('');
        }

        function isStepReached(order, step, stepIndex, statusIndex) {
            if (step.key === 'PAID') {
                return order.status === 'PAID' || getLatestPaymentStatus(order) === 'SUCCESS';
            }

            return stepIndex <= statusIndex;
        }

        function humanizeStatus(status) {
            return statusDisplayMap[status] ?? status.replace(/_/g, ' ');
        }

        function resolveStatusIndex(status) {
            return statusPriority[status] ?? 0;
        }

        function getLatestPaymentStatus(order) {
            if (order.latest_payment_status) {
                return order.latest_payment_status;
            }

            const latest = determineLatestPayment(order);
            return latest?.status ?? null;
        }

        function buildPaymentWarning(order) {
            if (order.status === 'PAID') {
                return '';
            }

            const latestStatus = getLatestPaymentStatus(order);

            if (! latestStatus || latestStatus === 'SUCCESS') {
                return '';
            }

            const message = latestStatus === 'FAILED'
                ? 'Pembayaran sebelumnya gagal. Mohon lakukan pelunasan di kasir.'
                : 'Pembayaran belum selesai. Mohon melunasi di kasir.';

            return `
                <div class="flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-medium text-amber-700">
                    <svg class="h-4 w-4 text-amber-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M8.257 3.099a1 1 0 011.486 0l5.579 9.92c.444.79-.111 1.781-.993 1.781H3.672c-.882 0-1.437-.991-.993-1.78l5.578-9.921zM10 7a.75.75 0 00-.75.75v3.5a.75.75 0 001.5 0v-3.5A.75.75 0 0010 7zm0 6a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                    </svg>
                    <span>${message}</span>
                </div>
            `;
        }

        function formatShortDate(value) {
            const parsed = new Date(value);

            if (Number.isNaN(parsed.getTime())) {
                return '';
            }

            return parsed.toLocaleString('id-ID', {
                day: 'numeric',
                month: 'short',
                hour: '2-digit',
                minute: '2-digit',
            });
        }

        function bootstrapInitialOrders() {
            if (! Array.isArray(initialOrders)) {
                return;
            }

            initialOrders.forEach((order) => {
                appendStatus(order);
            });
        }

        bootstrapInitialOrders();

        if (window.Echo) {
            window.Echo.channel('orders').listen('.order.updated', (event) => {
                if (event.table_code !== tableCode) {
                    return;
                }
                appendStatus(event);
            });
        }
    </script>
@endsection
