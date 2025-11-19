@extends('layouts.app')

@section('content')
    <div class="space-y-8">
        <section class="rounded-3xl bg-gradient-to-r from-slate-900 via-slate-800 to-slate-700 px-8 py-10 text-white shadow-xl shadow-slate-900/40">
            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div class="space-y-3">
                    <p class="text-sm uppercase tracking-[0.35em] text-white/60">Checkout</p>
                    <h1 class="text-3xl font-semibold">Pesanan Meja {{ $table->number }}</h1>
                    <p class="text-sm text-white/70">Sesi dimulai {{ $session->started_at->translatedFormat('l, d F Y · H:i') }}</p>
                </div>
                <div class="flex flex-col items-start gap-3 md:items-end">
                    <span class="inline-flex rounded-full bg-white/10 px-4 py-1 text-xs font-semibold uppercase tracking-wide text-white">{{ $session->status }}</span>
                    <a href="{{ route('customer.table', $table->code) }}"
                        class="inline-flex items-center gap-2 rounded-full border border-white/40 bg-white/10 px-4 py-2 text-xs font-semibold text-white/90 transition hover:bg-white/20">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m15 18-6-6 6-6" />
                        </svg>
                        Kembali ke menu
                    </a>
                </div>
            </div>
        </section>

        <div id="session-success" class="{{ empty($completionMessage) ? 'hidden' : 'flex' }} items-center gap-3 rounded-3xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700 shadow-sm shadow-emerald-200/60">
            <svg class="h-5 w-5 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 13l4 4L19 7" />
            </svg>
            <span data-message>{{ $completionMessage ?? 'Pembayaran berhasil diproses.' }}</span>
        </div>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1.6fr)_minmax(0,1fr)]">
            <section class="space-y-6">
                <div class="rounded-3xl border border-slate-200/80 bg-white/90 p-6 shadow-sm shadow-slate-900/5">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-800">Ringkasan Keranjang</h2>
                            <p class="text-sm text-slate-500">Pastikan semua pesanan sudah sesuai sebelum dikirim ke dapur.</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Total item: <span id="cart-total-count">0</span>
                            </span>
                            <button id="clear-cart" type="button" class="inline-flex items-center gap-2 rounded-full border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-500 transition hover:border-rose-300 hover:bg-rose-50">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 6h18" />
                                    <path d="M8 6v14c0 .55.45 1 1 1h6c.55 0 1-.45 1-1V6" />
                                    <path d="M10 11v6" />
                                    <path d="M14 11v6" />
                                    <path d="M9 6l1-3h4l1 3" />
                                </svg>
                                Kosongkan
                            </button>
                        </div>
                    </div>

                    <div id="cart-empty" class="mt-6 rounded-2xl border border-dashed border-slate-200 px-5 py-6 text-center text-sm text-slate-500">
                        Keranjang masih kosong. Silakan kembali ke menu untuk menambahkan pesanan.
                    </div>
                    <div id="cart-items" class="mt-6 hidden space-y-4 text-sm"></div>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl bg-slate-50/80 px-4 py-4 text-sm text-slate-600">
                            <div class="flex justify-between">
                                <span>Subtotal</span>
                                <span id="subtotal">Rp 0</span>
                            </div>
                            <div class="mt-2 flex justify-between text-slate-500">
                                <span>Pajak (10%)</span>
                                <span id="tax">Rp 0</span>
                            </div>
                            <div class="mt-2 flex justify-between text-slate-500">
                                <span>Service (5%)</span>
                                <span id="service">Rp 0</span>
                            </div>
                            <div class="mt-3 flex justify-between border-t border-slate-200 pt-3 text-base font-semibold text-slate-800">
                                <span>Total</span>
                                <span id="grand-total">Rp 0</span>
                            </div>
                        </div>

                        <form id="order-form" class="flex flex-col justify-between space-y-4 rounded-2xl border border-slate-200 px-4 py-4 text-sm text-slate-600">
                            @csrf
                            <label class="block">
                                <span class="font-medium text-slate-700">Catatan untuk dapur</span>
                                <textarea name="notes" rows="3" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-inner focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-100" placeholder="Contoh: kurang pedas, sambal pisah"></textarea>
                            </label>
                            <label class="block">
                                <span class="font-medium text-slate-700">Metode pembayaran</span>
                                <select name="payment_method" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-100">
                                    <option value="CASH">Bayar di kasir</option>
                                    <option value="QRIS">QRIS</option>
                                </select>
                            </label>
                            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-emerald-500 px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-emerald-500/40 transition hover:bg-emerald-600 disabled:cursor-not-allowed disabled:bg-slate-300" disabled>
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 5v14" />
                                    <path d="M5 12h14" />
                                </svg>
                                Kirim pesanan ke dapur
                            </button>
                        </form>
                    </div>

                    <div id="order-feedback" class="mt-4 text-sm text-slate-500"></div>

                    @php
                        $initialStatic = $qrisConfig['static_image_url'] ?? null;
                        $qrisPanelClasses = trim('mt-4 ' . ($initialStatic ? '' : 'hidden') . ' rounded-3xl border border-emerald-100 bg-emerald-50/80 p-5 text-sm text-slate-600');
                    @endphp
                    <div id="qris-panel" data-static-src="{{ $initialStatic }}" class="{{ $qrisPanelClasses }}">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div class="space-y-2">
                                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-emerald-500">QRIS Dinamis</p>
                                <h3 class="text-lg font-semibold text-slate-800">Menunggu pembayaran</h3>
                                <p data-qris-status class="text-sm text-slate-600">
                                    {{ $initialStatic ? 'Scan QR berikut dan masukkan nominal sesuai total.' : 'Pilih metode QRIS saat checkout untuk memunculkan kode.' }}
                                </p>
                                <p class="text-base font-semibold text-slate-900">Total: <span data-qris-amount>Rp 0</span></p>
                                <button type="button" data-qris-refresh class="hidden inline-flex items-center gap-2 rounded-full border border-emerald-300 px-4 py-2 text-xs font-semibold text-emerald-700 transition hover:bg-white">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 4v6h6" />
                                        <path d="M20 20v-6h-6" />
                                        <path d="M5 15a7 7 0 0 0 12 2" />
                                        <path d="M19 9a7 7 0 0 0-12-2" />
                                    </svg>
                                    Buat ulang QR
                                </button>
                                <button type="button" data-qris-download class="inline-flex items-center gap-2 rounded-full border border-transparent bg-white px-4 py-2 text-xs font-semibold text-emerald-600 shadow-sm transition hover:border-emerald-200 hover:bg-emerald-50 disabled:cursor-not-allowed disabled:opacity-40" {{ $initialStatic ? '' : 'disabled' }}>
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 3v12" />
                                        <path d="m8 11 4 4 4-4" />
                                        <path d="M5 21h14" />
                                    </svg>
                                    Download QR
                                </button>
                            </div>
                            <div class="flex flex-col items-center gap-3">
                                <div data-qris-qr class="flex h-64 w-64 items-center justify-center rounded-3xl border border-white/80 bg-white/80 p-4 text-center text-xs text-slate-400 shadow-inner shadow-emerald-100/80">
                                    @if ($initialStatic)
                                        <img src="{{ $initialStatic }}" alt="QRIS statis" class="h-full w-full rounded-xl object-cover" />
                                    @else
                                        QRIS akan muncul di sini setelah pesanan dengan metode QRIS berhasil dibuat.
                                    @endif
                                </div>
                                <p data-qris-amount-inline class="text-base font-semibold text-slate-900">
                                    Nominal: Rp 0
                                </p>
                                <div data-qris-static class="{{ $initialStatic ? '' : 'hidden' }} text-center text-xs text-slate-500">
                                    <p>QR referensi statis</p>
                                    <img data-qris-static-img src="{{ $initialStatic }}" alt="QR statis" class="mx-auto mt-2 h-28 w-28 rounded-2xl border border-dashed border-slate-300 object-cover" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <aside class="space-y-6">
                <div class="rounded-3xl border border-slate-200/80 bg-white/90 p-6 shadow-sm shadow-slate-900/5">
                    <h2 class="text-lg font-semibold text-slate-800">Status Pesanan</h2>
                    <p class="mt-1 text-sm text-slate-500">Pantau progres pesanan yang sudah dikirim.</p>
                    <ul id="order-status-list" class="mt-4 space-y-3 text-sm text-slate-600">
                        <li>Belum ada pesanan.</li>
                    </ul>
                </div>
            </aside>
        </div>
    </div>

    <script>
        (function () {
            const tableCode = @json($table->code);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const cartKey = `kahuripan-cart-${tableCode}`;
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
            const clearCartBtn = document.getElementById('clear-cart');
            const countBadge = document.getElementById('cart-total-count');
            const sessionSuccess = document.getElementById('session-success');
            const sessionSuccessMessage = sessionSuccess ? sessionSuccess.querySelector('[data-message]') : null;
            const initialCompletionMessage = @json($completionMessage);
            const orderStatuses = new Map();
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
            let hasShownSuccess = false;
            const paymentInitiateUrl = '{{ route('payment.initiate') }}';
            const qrisPanel = document.getElementById('qris-panel');
            const qrisStatusEl = qrisPanel ? qrisPanel.querySelector('[data-qris-status]') : null;
            const qrisAmountEl = qrisPanel ? qrisPanel.querySelector('[data-qris-amount]') : null;
            const qrisQrContainer = qrisPanel ? qrisPanel.querySelector('[data-qris-qr]') : null;
            const qrisStaticWrapper = qrisPanel ? qrisPanel.querySelector('[data-qris-static]') : null;
            const qrisStaticImage = qrisPanel ? qrisPanel.querySelector('[data-qris-static-img]') : null;
            const qrisRefreshBtn = qrisPanel ? qrisPanel.querySelector('[data-qris-refresh]') : null;
            const qrisDownloadBtn = qrisPanel ? qrisPanel.querySelector('[data-qris-download]') : null;
            const qrisAmountInline = qrisPanel ? qrisPanel.querySelector('[data-qris-amount-inline]') : null;
            const initialQrisStatic = @json($qrisConfig['static_image_url']);
            let activeQrisOrderId = null;
            let qrisDownloadSource = initialQrisStatic
                ? { type: 'image', value: initialQrisStatic }
                : null;

            function firstNonNull() {
                for (let index = 0; index < arguments.length; index += 1) {
                    const value = arguments[index];
                    if (value !== undefined && value !== null) {
                        return value;
                    }
                }

                return null;
            }

            function signatureFor(item) {
                const options = Array.isArray(item.options) ? item.options : [];
                const optionKeys = options
                    .map((option) => {
                        if (option && typeof option === 'object') {
                            if (option.id !== undefined && option.id !== null) {
                                return option.id;
                            }

                            if (option.name !== undefined && option.name !== null) {
                                return option.name;
                            }
                        }

                        return option;
                    })
                    .map((value) => String(value))
                    .sort();

                return JSON.stringify({
                    menu_id: item.menu_id,
                    options: optionKeys,
                });
            }

            function loadCart() {
                try {
                    const stored = localStorage.getItem(cartKey);

                    if (! stored) {
                        return [];
                    }

                    const parsed = JSON.parse(stored);

                    if (! Array.isArray(parsed)) {
                        return [];
                    }

                    return parsed
                        .filter((item) => item && item.menu_id)
                        .map((item) => ({
                            signature: item.signature !== undefined && item.signature !== null ? item.signature : signatureFor(item),
                            menu_id: item.menu_id,
                            name: item.name !== undefined && item.name !== null ? item.name : '',
                            price: Number(item.price || 0),
                            options: Array.isArray(item.options) ? item.options : [],
                            optionTotal: Number(item.optionTotal || 0),
                            qty: Math.max(Number(item.qty || 1), 1),
                            notes: item.notes !== undefined && item.notes !== null ? item.notes : '',
                        }));
                } catch (error) {
                    console.warn('Gagal memuat keranjang.', error);
                    return [];
                }
            }

            const cart = loadCart();
            resetQrisPanel();

            function persistCart() {
                try {
                    localStorage.setItem(cartKey, JSON.stringify(cart));
                } catch (error) {
                    console.warn('Gagal menyimpan keranjang.', error);
                }

                updateCountBadge();
            }

            if (initialCompletionMessage && sessionSuccess && sessionSuccessMessage) {
                sessionSuccess.classList.remove('hidden');
                sessionSuccess.classList.add('flex');
                sessionSuccessMessage.textContent = initialCompletionMessage;
                hasShownSuccess = true;

                setTimeout(() => {
                    sessionSuccess.classList.add('hidden');
                    sessionSuccess.classList.remove('flex');
                    hasShownSuccess = false;
                }, 5000);
            }

            function formatCurrency(value) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value);
            }

            function updateQrisAmountDisplay(amount) {
                const formatted = formatCurrency(amount);

                if (qrisAmountEl) {
                    qrisAmountEl.textContent = formatted;
                }

                if (qrisAmountInline) {
                    qrisAmountInline.textContent = `Nominal: ${formatted}`;
                }
            }

            function setQrisDownloadSource(source) {
                qrisDownloadSource = source;

                if (qrisDownloadBtn) {
                    qrisDownloadBtn.disabled = ! source;
                }
            }

            function triggerDownload(url, filename) {
                const link = document.createElement('a');
                link.href = url;
                link.download = filename;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }

            function renderQrPlaceholder() {
                if (! qrisQrContainer) {
                    return;
                }

                qrisQrContainer.innerHTML = '<p class="text-center text-xs text-slate-400">QRIS akan muncul di sini setelah pesanan dengan metode QRIS berhasil dibuat.</p>';
            }

            function renderQrImage(imageUrl) {
                if (! qrisQrContainer) {
                    return;
                }

                if (imageUrl) {
                    qrisQrContainer.innerHTML = `<img src="${imageUrl}" alt="QRIS" class="h-full w-full rounded-xl object-cover" />`;
                    return;
                }

                renderQrPlaceholder();
            }

            function resetQrisPanel() {
                if (! qrisPanel) {
                    return;
                }

                activeQrisOrderId = null;

                if (initialQrisStatic) {
                    qrisPanel.classList.remove('hidden');
                } else {
                    qrisPanel.classList.add('hidden');
                }

                if (qrisStatusEl) {
                    qrisStatusEl.textContent = initialQrisStatic
                        ? 'Scan QR berikut dan masukkan nominal sesuai total.'
                        : 'Pilih metode QRIS saat checkout untuk memunculkan kode.';
                }

                updateQrisAmountDisplay(0);

                if (initialQrisStatic) {
                    renderQrImage(initialQrisStatic);
                } else {
                    renderQrPlaceholder();
                }

                if (qrisStaticWrapper && qrisStaticImage) {
                    if (initialQrisStatic) {
                        qrisStaticImage.src = initialQrisStatic;
                        qrisStaticWrapper.classList.remove('hidden');
                    } else {
                        qrisStaticWrapper.classList.add('hidden');
                    }
                }

                if (qrisRefreshBtn) {
                    qrisRefreshBtn.classList.add('hidden');
                }

                setQrisDownloadSource(initialQrisStatic ? { type: 'image', value: initialQrisStatic } : null);
            }

            async function initiateQris(orderId) {
                if (! qrisPanel || ! orderId) {
                    return;
                }

                activeQrisOrderId = orderId;
                qrisPanel.classList.remove('hidden');

                if (qrisStatusEl) {
                    qrisStatusEl.textContent = 'Menyiapkan QRIS...';
                }

                if (qrisQrContainer) {
                    qrisQrContainer.innerHTML = '<p class="text-center text-xs text-slate-400">Menyiapkan QR...</p>';
                }

                try {
                    const response = await fetch(paymentInitiateUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            order_id: orderId,
                            method: 'QRIS',
                        }),
                    });

                    if (! response.ok) {
                        const error = await response.json().catch(() => ({ message: 'Tidak bisa membuat QRIS.' }));
                        throw new Error(error.message || 'Tidak bisa membuat QRIS.');
                    }

                    const data = await response.json();

                    updateQrisAmountDisplay(data.amount || 0);

                    const dynamicImageSrc = data.qr_svg_data_url || null;

                    if (dynamicImageSrc) {
                        renderQrImage(dynamicImageSrc);
                    } else if (data.qr_svg) {
                        qrisQrContainer.innerHTML = data.qr_svg;
                    } else if (data.static_image_url || initialQrisStatic) {
                        renderQrImage(data.static_image_url || initialQrisStatic);
                    } else if (qrisQrContainer) {
                        qrisQrContainer.innerHTML = '<p class="text-center text-xs text-rose-500">QR tidak tersedia.</p>';
                    }

                    if (qrisStatusEl) {
                        qrisStatusEl.textContent = data.is_dynamic
                            ? 'Scan QR ini menggunakan aplikasi pembayaran Anda.'
                            : 'QR fallback digunakan. Mohon cek nominal sebelum membayar.';
                    }

                    if (data.static_image_url && qrisStaticWrapper && qrisStaticImage) {
                        qrisStaticImage.src = data.static_image_url;
                        qrisStaticWrapper.classList.remove('hidden');
                    } else if (qrisStaticWrapper) {
                        qrisStaticWrapper.classList.add('hidden');
                    }

                    if (qrisRefreshBtn) {
                        qrisRefreshBtn.classList.remove('hidden');
                    }

                    const downloadSource = dynamicImageSrc
                        ? { type: 'data-url', value: dynamicImageSrc }
                        : data.qr_svg
                            ? { type: 'svg', value: data.qr_svg }
                            : data.static_image_url
                                ? { type: 'image', value: data.static_image_url }
                                : initialQrisStatic
                                    ? { type: 'image', value: initialQrisStatic }
                                    : null;

                    setQrisDownloadSource(downloadSource);

                    feedbackEl.textContent = 'Silakan lanjutkan pembayaran melalui QRIS.';
                } catch (error) {
                    if (qrisStatusEl) {
                        qrisStatusEl.textContent = error.message || 'Gagal membuat QRIS.';
                    }

                    if (initialQrisStatic) {
                        renderQrImage(initialQrisStatic);
                    } else if (qrisQrContainer) {
                        qrisQrContainer.innerHTML = '<p class="text-center text-xs text-rose-500">QR gagal dimuat.</p>';
                    }

                    setQrisDownloadSource(initialQrisStatic ? { type: 'image', value: initialQrisStatic } : null);
                }
            }

            function updateCountBadge() {
                const total = cart.reduce((sum, item) => sum + (Number(item.qty) || 0), 0);

                if (countBadge) {
                    countBadge.textContent = total;
                }

                submitBtn.disabled = total === 0;
            }

            function renderCart() {
                if (cart.length === 0) {
                    cartEmpty.classList.remove('hidden');
                    cartItemsContainer.classList.add('hidden');
                } else {
                    cartEmpty.classList.add('hidden');
                    cartItemsContainer.classList.remove('hidden');
                }

                cartItemsContainer.innerHTML = '';

                let subtotal = 0;

                cart.forEach((item, index) => {
                    const optionTotal = Number(item.optionTotal || 0);
                    const linePrice = (Number(item.price) + optionTotal) * item.qty;
                    subtotal += linePrice;
                    const wrapper = document.createElement('div');
                    wrapper.className = 'rounded-2xl border border-slate-200 px-4 py-3';
                    wrapper.innerHTML = `
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div class="space-y-1">
                                <p class="font-semibold text-slate-700">${item.name}</p>
                                <p class="text-xs text-slate-500">${formatCurrency(item.price)} × ${item.qty} porsi</p>
                                ${item.options && item.options.length ? `
                                    <p class="text-xs text-slate-400">Addon: ${item.options.map((option) => option.name).join(', ')}</p>
                                ` : ''}
                            </div>
                            <div class="flex items-center justify-between gap-4 sm:justify-end">
                                <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-600">
                                    <button type="button" class="flex h-6 w-6 items-center justify-center rounded-full bg-white text-slate-500 hover:text-emerald-600" data-action="decrease" data-index="${index}">
                                        <span class="text-lg leading-none">&minus;</span>
                                    </button>
                                    <span>${item.qty}</span>
                                    <button type="button" class="flex h-6 w-6 items-center justify-center rounded-full bg-white text-slate-500 hover:text-emerald-600" data-action="increase" data-index="${index}">
                                        <span class="text-lg leading-none">+</span>
                                    </button>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-slate-700">${formatCurrency(linePrice)}</p>
                                    <button type="button" data-action="remove" data-index="${index}" class="mt-2 inline-flex items-center gap-1 text-xs font-semibold text-rose-500 hover:text-rose-600">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m18 6-12 12" />
                                            <path d="m6 6 12 12" />
                                        </svg>
                                        Hapus
                                    </button>
                                </div>
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

                updateCountBadge();
            }

            function handleSessionCompletion(order) {
                if (orderStatuses.size > 0 || hasShownSuccess) {
                    return;
                }

                hasShownSuccess = true;
                hideQrisPanel();

                cart.length = 0;
                persistCart();
                renderCart();

                if (sessionSuccess) {
                    sessionSuccess.classList.remove('hidden');
                    sessionSuccess.classList.add('flex');

                    if (sessionSuccessMessage) {
                        const paymentInfo = order.latest_payment_method
                            ? `Pembayaran (${order.latest_payment_method}) telah dikonfirmasi.`
                            : 'Pembayaran telah dikonfirmasi.';
                        sessionSuccessMessage.textContent = `Pesanan ${order.code} selesai. ${paymentInfo}`;
                    }

                    setTimeout(() => {
                        sessionSuccess.classList.add('hidden');
                        sessionSuccess.classList.remove('flex');
                    }, 5000);
                }

                feedbackEl.textContent = 'Pembayaran berhasil dikonfirmasi. Terima kasih!';
            }

        cartItemsContainer.addEventListener('click', (event) => {
                const button = event.target.closest('button[data-action]');

                if (! button) {
                    return;
                }

                const index = Number(button.dataset.index);
                const action = button.dataset.action;
                const item = cart[index];

                if (! item) {
                    return;
                }

                if (action === 'remove') {
                    cart.splice(index, 1);
                }

                if (action === 'increase') {
                    item.qty += 1;
                }

                if (action === 'decrease') {
                    item.qty = Math.max(item.qty - 1, 0);

                    if (item.qty === 0) {
                        cart.splice(index, 1);
                    }
                }

                persistCart();
                renderCart();
            });

            if (clearCartBtn) {
                clearCartBtn.addEventListener('click', () => {
                    cart.length = 0;
                    persistCart();
                    renderCart();
                    feedbackEl.textContent = 'Keranjang dikosongkan.';
                });
            }

            if (qrisRefreshBtn) {
                qrisRefreshBtn.addEventListener('click', () => {
                    if (activeQrisOrderId) {
                        initiateQris(activeQrisOrderId);
                    }
                });
            }

            if (qrisDownloadBtn) {
                qrisDownloadBtn.addEventListener('click', () => {
                    if (! qrisDownloadSource) {
                        return;
                    }

                    if (qrisDownloadSource.type === 'svg') {
                        const blob = new Blob([qrisDownloadSource.value], { type: 'image/svg+xml' });
                        const url = URL.createObjectURL(blob);
                        triggerDownload(url, 'qr-dinamis.svg');
                        URL.revokeObjectURL(url);
                        return;
                    }

                    if (qrisDownloadSource.type === 'data-url') {
                        triggerDownload(qrisDownloadSource.value, 'qr-dinamis.svg');
                        return;
                    }

                    if (qrisDownloadSource.type === 'image') {
                        triggerDownload(qrisDownloadSource.value, 'qr-statis.png');
                    }
                });
            }

            orderForm.addEventListener('submit', async (event) => {
                event.preventDefault();

                if (cart.length === 0) {
                    feedbackEl.textContent = 'Silakan tambahkan menu sebelum mengirim pesanan.';
                    return;
                }

                submitBtn.disabled = true;
                feedbackEl.textContent = 'Mengirim pesanan...';

                const formData = new FormData(orderForm);
                const selectedMethod = formData.get('payment_method');
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
                        method: selectedMethod,
                    },
                };

                try {
                    const response = await fetch('{{ route('order.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify(payload),
                    });

                    if (! response.ok) {
                        const error = await response.json().catch(() => ({ message: 'Terjadi kesalahan saat mengirim pesanan.' }));
                        throw new Error(error.message || 'Terjadi kesalahan saat mengirim pesanan.');
                    }

                    const data = await response.json();
                    cart.length = 0;
                    persistCart();
                    renderCart();

                    const orderData = data && typeof data === 'object' && data.order ? data.order : {};
                    const order = Object.assign({}, orderData, {
                        table_code: tableCode,
                        created_at: orderData && orderData.created_at ? orderData.created_at : new Date().toISOString(),
                    });

                    feedbackEl.textContent = data && data.message ? data.message : 'Pesanan berhasil dikirim.';
                    appendStatus(order);

                    if (selectedMethod === 'QRIS') {
                        await initiateQris(order.id);
                } else {
                    resetQrisPanel();
                }
                } catch (error) {
                    feedbackEl.textContent = error.message || 'Terjadi kesalahan saat mengirim pesanan.';
                } finally {
                    submitBtn.disabled = cart.length === 0;
                }
            });

            function appendStatus(order) {
                if (order.table_code && order.table_code !== tableCode) {
                    return;
                }

                const current = orderStatuses.get(order.id) || {};
                const merged = enrichOrderData(current, order);
                const recordId = merged.id !== undefined && merged.id !== null ? merged.id : order.id;

                if (! isOrderActive(merged.status)) {
                    orderStatuses.delete(recordId);
                    renderStatusList();
                    handleSessionCompletion(merged);
                    return;
                }

                orderStatuses.set(recordId, merged);
                hasShownSuccess = false;
                if (sessionSuccess) {
                    sessionSuccess.classList.add('hidden');
                    sessionSuccess.classList.remove('flex');
                }
                renderStatusList();

                if (activeQrisOrderId && merged.id && Number(merged.id) === Number(activeQrisOrderId) && merged.latest_payment_status === 'SUCCESS') {
                    resetQrisPanel();
                }
            }

            function isOrderActive(status) {
                return ! finalOrderStatuses.has(status);
            }

            function enrichOrderData(existing, incoming) {
                const mergedPayments = Array.isArray(incoming.payments) && incoming.payments.length > 0
                    ? incoming.payments
                    : existing.payments || [];

                const merged = Object.assign({}, existing, incoming, {
                    payments: mergedPayments,
                });

                if (! merged.table_code) {
                    merged.table_code = tableCode;
                }

                merged.created_at = merged.created_at || existing.created_at || null;

                const latestPayment = determineLatestPayment(merged);

                merged.latest_payment_status = firstNonNull(
                    incoming.latest_payment_status,
                    existing.latest_payment_status,
                    latestPayment ? latestPayment.status : null
                );

                merged.latest_payment_method = firstNonNull(
                    incoming.latest_payment_method,
                    existing.latest_payment_method,
                    latestPayment ? latestPayment.method : null
                );

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
                return statusDisplayMap[status] !== undefined ? statusDisplayMap[status] : status.replace(/_/g, ' ');
            }

            function resolveStatusIndex(status) {
                return statusPriority[status] !== undefined ? statusPriority[status] : 0;
            }

            function getLatestPaymentStatus(order) {
                if (order.latest_payment_status) {
                    return order.latest_payment_status;
                }

                const latest = determineLatestPayment(order);
                return latest ? latest.status : null;
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
            renderCart();

            if (window.Echo) {
                window.Echo.channel('orders').listen('.order.updated', (event) => {
                    if (event.table_code !== tableCode) {
                        return;
                    }
                    appendStatus(event);
                });
            }
        })();
    </script>
@endsection
