@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
@endphp

@section('content')
    <div class="space-y-8">
        <section class="rounded-3xl bg-gradient-to-r from-emerald-500 via-emerald-400 to-teal-400 px-8 py-10 text-white shadow-xl shadow-emerald-500/30">
            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div class="space-y-3">
                    <p class="text-sm uppercase tracking-[0.35em] text-white/70">Sesi Meja</p>
                    <h1 class="text-3xl font-semibold">Meja {{ $table->number }}</h1>
                    <p class="text-sm text-white/80">Kode QR: <span class="font-medium text-white">{{ $table->code }}</span></p>
                    <p class="text-sm text-white/80">Mulai {{ $session->started_at->translatedFormat('l, d F Y · H:i') }}</p>
                </div>
                <div class="flex flex-col items-start gap-3 md:items-end">
                    <span class="inline-flex rounded-full bg-white/15 px-4 py-1 text-xs font-semibold uppercase tracking-wide text-white">{{ $session->status }}</span>
                    <a href="{{ route('customer.checkout', $table->code) }}"
                        data-cart-link
                        class="inline-flex items-center gap-2 rounded-full border border-white/40 bg-white/10 px-4 py-2 text-sm font-semibold text-white/90 transition hover:bg-white/20">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 3h18" />
                            <path d="M3 9h18" />
                            <path d="M3 15h12" />
                            <path d="M3 21h6" />
                        </svg>
                        <span>Lihat keranjang</span>
                        <span id="cart-count-top" class="hidden min-w-[1.6rem] rounded-full bg-white/20 px-2 py-0.5 text-center text-xs font-semibold text-white shadow-sm">0</span>
                    </a>
                </div>
            </div>
        </section>

        @foreach ($menus as $category => $items)
            <section class="space-y-4">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-800">{{ $category }}</h2>
                        <p class="text-sm text-slate-500">{{ $items->count() }} pilihan tersedia</p>
                    </div>
                </div>

                <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
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

                            $imageSource = null;

                            if ($menu->image_url) {
                                $imageSource = Str::startsWith($menu->image_url, ['http://', 'https://'])
                                    ? $menu->image_url
                                    : Storage::url($menu->image_url);
                            }

                            $menuDescription = Str::limit($menu->description ?: 'Menu spesial pilihan chef kami.', 120);
                        @endphp
                        <article class="group flex h-full flex-col overflow-hidden rounded-3xl border border-slate-200/60 bg-white shadow-sm shadow-slate-900/5 transition hover:-translate-y-1 hover:border-emerald-300 hover:shadow-lg">
                            <div class="relative aspect-[4/3] w-full overflow-hidden bg-slate-100">
                                @if ($imageSource)
                                    <img src="{{ $imageSource }}" alt="{{ $menu->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                @else
                                    <div class="flex h-full w-full flex-col items-center justify-center gap-2 text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        <svg class="h-8 w-8 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                            <polyline points="21 15 16 10 5 21"></polyline>
                                        </svg>
                                        <span>Foto belum tersedia</span>
                                    </div>
                                @endif
                                <span class="absolute bottom-3 left-4 inline-flex items-center rounded-full bg-white/95 px-4 py-1 text-sm font-semibold text-emerald-600 shadow-sm shadow-emerald-500/20">
                                    Rp {{ number_format($menu->price, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex flex-1 flex-col justify-between p-6">
                                <div class="space-y-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <h3 class="text-base font-semibold text-slate-800">{{ $menu->name }}</h3>
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                                            Stok {{ max($menu->stock, 0) }}
                                        </span>
                                    </div>
                                    <p class="text-sm leading-relaxed text-slate-500">{{ $menuDescription }}</p>

                                    @if ($menu->options->isNotEmpty())
                                        <div class="flex flex-wrap gap-2 pt-1">
                                            @foreach ($menu->options as $option)
                                                <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-600">
                                                    {{ $option->name }}
                                                    @if ($option->extra_price)
                                                        <span class="text-emerald-500">+Rp {{ number_format($option->extra_price, 0, ',', '.') }}</span>
                                                    @endif
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="mt-6 space-y-2">
                                    <button
                                        data-menu='@json($menuPayload)'
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-emerald-500 px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-emerald-500/30 transition hover:bg-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-white">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <circle cx="9" cy="21" r="1"></circle>
                                            <circle cx="20" cy="21" r="1"></circle>
                                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                        </svg>
                                        Tambah ke Keranjang
                                    </button>
                                    @if ($menu->options->isNotEmpty())
                                        <button type="button"
                                            data-menu-addons='@json($menuPayload)'
                                            class="inline-flex w-full items-center justify-center gap-2 rounded-full border border-emerald-200 bg-white px-5 py-3 text-sm font-semibold text-emerald-600 shadow-sm shadow-emerald-500/10 transition hover:border-emerald-300 hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:ring-offset-2 focus:ring-offset-white">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M12 5v14" />
                                                <path d="M5 12h14" />
                                            </svg>
                                            Pilih addon
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>

    <a href="{{ route('customer.checkout', $table->code) }}"
        data-cart-trigger
        class="group fixed bottom-6 right-6 inline-flex items-center gap-3 rounded-full bg-emerald-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-500/40 transition hover:bg-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-white lg:hidden">
        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-white/15 text-white">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 3h2l.4 2M7 13h10l1.5-6H5.4" />
                <circle cx="9" cy="20" r="1" />
                <circle cx="18" cy="20" r="1" />
            </svg>
        </span>
        <span>Keranjang</span>
        <span id="cart-count" class="hidden h-6 min-w-[1.5rem] items-center justify-center rounded-full bg-white/90 px-2 text-xs font-semibold text-emerald-600 shadow-sm shadow-emerald-200">0</span>
    </a>

    <div id="cart-toast" class="pointer-events-none fixed bottom-24 right-6 hidden min-w-[200px] max-w-xs translate-y-2 items-center gap-2 rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-900/40 opacity-0 transition duration-200 ease-out lg:right-10">
        <svg class="h-4 w-4 flex-none text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 13l4 4L19 7" />
        </svg>
        <span id="cart-toast-message">Ditambahkan ke keranjang</span>
    </div>

    <dialog id="addon-modal" class="w-full max-w-lg rounded-3xl border border-slate-200 bg-white p-0 shadow-2xl shadow-slate-900/30">
        <div class="space-y-5 p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 id="addon-modal-title" class="text-lg font-semibold text-slate-800">Pilih addon</h3>
                    <p id="addon-modal-description" class="mt-1 text-sm text-slate-500"></p>
                </div>
                <button type="button" data-addon-close class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 text-slate-400 transition hover:border-slate-300 hover:text-slate-600">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m9 9 6 6" />
                        <path d="m15 9-6 6" />
                    </svg>
                </button>
            </div>
            <form id="addon-form" class="space-y-5">
                <div id="addon-options" class="space-y-3">
                    <p class="text-sm text-slate-400">Menu ini belum memiliki addon.</p>
                </div>
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <span class="text-sm font-semibold text-slate-600">
                        Total addon: <span id="addon-total">Rp 0</span>
                    </span>
                    <div class="flex gap-2">
                        <button type="button" data-addon-cancel class="inline-flex items-center rounded-full border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-500 transition hover:border-slate-300 hover:text-slate-700">
                            Batal
                        </button>
                        <button type="submit" class="inline-flex items-center gap-2 rounded-full bg-emerald-500 px-5 py-2 text-xs font-semibold text-white shadow-sm shadow-emerald-500/30 transition hover:bg-emerald-600">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 5v14" />
                                <path d="M5 12h14" />
                            </svg>
                            Tambahkan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </dialog>

    <script>
        (function () {
            const tableCode = @json($table->code);
            const cartKey = `kahuripan-cart-${tableCode}`;
            const trigger = document.querySelector('[data-cart-trigger]');
            const cartBadge = document.getElementById('cart-count');
            const topCartLink = document.querySelector('[data-cart-link]');
            const cartBadgeTop = document.getElementById('cart-count-top');
            const toast = document.getElementById('cart-toast');
            const toastMessage = document.getElementById('cart-toast-message');
            const addonModal = document.getElementById('addon-modal');
            const addonForm = document.getElementById('addon-form');
            const addonOptions = document.getElementById('addon-options');
            const addonTitle = document.getElementById('addon-modal-title');
            const addonDescription = document.getElementById('addon-modal-description');
            const addonTotal = document.getElementById('addon-total');
            const addonDismissButtons = addonModal ? addonModal.querySelectorAll('[data-addon-close], [data-addon-cancel]') : [];
            const currencyFormatter = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0,
            });
            let toastTimeout = null;
            let activeMenu = null;

            function signatureFor(menuId, options) {
                const source = Array.isArray(options) ? options : [];
                const optionKeys = source
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
                    .map((value) => String(value));

                optionKeys.sort();

                return JSON.stringify({
                    menu_id: menuId,
                    options: optionKeys,
                });
            }

            function formatCurrency(value) {
                return currencyFormatter.format(Number(value) || 0);
            }

            function updateAddonTotal() {
                if (! addonOptions || ! addonTotal) {
                    return;
                }

                let total = 0;

                addonOptions.querySelectorAll('input[type="checkbox"]:checked').forEach((input) => {
                    const priceValue = input.dataset && input.dataset.price ? input.dataset.price : 0;
                    total += Number(priceValue || 0);
                });

                addonTotal.textContent = formatCurrency(total);
            }

            function openAddonModal(menu) {
                if (! addonModal || ! addonForm || ! addonOptions) {
                    return;
                }

                activeMenu = menu;
                addonTitle.textContent = `Addon ${menu.name}`;
                addonDescription.textContent = 'Pilih tambahan yang ingin disertakan untuk menu ini.';
                addonOptions.innerHTML = '';

                const options = Array.isArray(menu.options) ? menu.options : [];

                if (options.length === 0) {
                    addonOptions.innerHTML = '<p class="rounded-2xl border border-dashed border-slate-200 px-4 py-3 text-sm text-slate-400">Menu ini belum memiliki addon.</p>';
                } else {
                    options.forEach((option) => {
                        const optionIdentifier = option && option.id !== undefined && option.id !== null
                            ? option.id
                            : (option && option.name !== undefined && option.name !== null ? option.name : '');
                        const inputId = `addon-${menu.id}-${optionIdentifier}`.replace(/[^a-zA-Z0-9-_]/g, '');
                        const wrapper = document.createElement('label');
                        wrapper.className = 'flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm';

                        wrapper.innerHTML = `
                            <span class="flex items-center gap-3">
                                <input type="checkbox" id="${inputId}" value="${optionIdentifier}"
                                    data-name="${option.name}"
                                    data-price="${Number(option.extra_price || 0)}"
                                    class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                <span class="font-medium text-slate-700">${option.name}</span>
                            </span>
                            <span class="text-xs font-semibold text-emerald-600">+${formatCurrency(option.extra_price || 0)}</span>
                        `;

                        const input = wrapper.querySelector('input');
                        input.addEventListener('change', updateAddonTotal);

                        addonOptions.appendChild(wrapper);
                    });
                }

                updateAddonTotal();

                if (typeof addonModal.showModal === 'function') {
                    addonModal.showModal();
                } else {
                    addonModal.setAttribute('open', 'true');
                    addonModal.classList.add('open');
                }
            }

            function closeAddonModal() {
                if (! addonModal) {
                    return;
                }

                activeMenu = null;
                if (addonOptions) {
                    addonOptions.querySelectorAll('input[type="checkbox"]').forEach((input) => {
                        input.checked = false;
                    });
                    updateAddonTotal();
                }

                if (typeof addonModal.close === 'function') {
                    addonModal.close();
                } else {
                    addonModal.removeAttribute('open');
                    addonModal.classList.remove('open');
                }
            }

            function migrateCart(raw) {
                if (! Array.isArray(raw)) {
                    return [];
                }

                return raw
                    .filter((item) => item && item.menu_id)
                    .map((item) => ({
                        signature: item.signature !== undefined && item.signature !== null
                            ? item.signature
                            : signatureFor(item.menu_id, item.options),
                        menu_id: item.menu_id,
                        name: item.name !== undefined && item.name !== null ? item.name : '',
                        price: Number(item.price || 0),
                        options: Array.isArray(item.options) ? item.options : [],
                        optionTotal: Number(item.optionTotal || 0),
                        qty: Math.max(Number(item.qty || 1), 1),
                        notes: item.notes !== undefined && item.notes !== null ? item.notes : '',
                    }));
            }

            function loadCart() {
                try {
                    const stored = localStorage.getItem(cartKey);

                    if (! stored) {
                        return [];
                    }

                    const parsed = JSON.parse(stored);

                    return migrateCart(parsed);
                } catch (error) {
                    console.warn('Gagal memuat keranjang dari penyimpanan lokal.', error);
                    return [];
                }
            }

            let cart = loadCart();

            function updateBadge() {
                const totalItems = cart.reduce((sum, item) => sum + (Number(item.qty) || 0), 0);

                if (cartBadge) {
                    cartBadge.textContent = totalItems;
                    cartBadge.classList.toggle('hidden', totalItems === 0);
                }

                if (cartBadgeTop) {
                    cartBadgeTop.textContent = totalItems;
                    cartBadgeTop.classList.toggle('hidden', totalItems === 0);
                }

                if (trigger) {
                    trigger.classList.toggle('opacity-80', totalItems === 0);
                }

                if (topCartLink) {
                    topCartLink.classList.toggle('opacity-80', totalItems === 0);
                }
            }

            function persistCart() {
                try {
                    localStorage.setItem(cartKey, JSON.stringify(cart));
                } catch (error) {
                    console.warn('Gagal menyimpan keranjang.', error);
                }

                updateBadge();
            }

            function showToast(message) {
                if (! toast || ! toastMessage) {
                    return;
                }

                toastMessage.textContent = message;
                toast.classList.remove('hidden');
                toast.classList.add('flex');
                requestAnimationFrame(() => {
                    toast.classList.remove('opacity-0', 'translate-y-2');
                    toast.classList.add('opacity-100', '-translate-y-2');
                });

                clearTimeout(toastTimeout);
                toastTimeout = setTimeout(() => {
                    toast.classList.remove('opacity-100', '-translate-y-2');
                    toast.classList.add('opacity-0', 'translate-y-2');
                    setTimeout(() => toast.classList.add('hidden'), 200);
                }, 1800);
            }

            if (addonModal) {
                addonModal.addEventListener('cancel', (event) => {
                    event.preventDefault();
                    closeAddonModal();
                });
            }

            addonDismissButtons.forEach((button) => {
                button.addEventListener('click', () => closeAddonModal());
            });

            document.querySelectorAll('[data-menu]').forEach((button) => {
                button.addEventListener('click', () => {
                    const data = JSON.parse(button.dataset.menu);
                    const signature = signatureFor(data.id, []);
                    const existing = cart.find((item) => item.signature === signature);

                    if (existing) {
                        existing.qty = Number(existing.qty || 0) + 1;
                    } else {
                        cart.push({
                            signature,
                            menu_id: data.id,
                            name: data.name,
                            price: Number(data.price),
                            options: [],
                            optionTotal: 0,
                            qty: 1,
                            notes: '',
                        });
                    }

                    persistCart();
                    showToast(`${data.name} ditambahkan ke keranjang`);
                });
            });

            document.querySelectorAll('[data-menu-addons]').forEach((button) => {
                button.addEventListener('click', () => {
                    const data = JSON.parse(button.dataset.menuAddons);
                    openAddonModal(data);
                });
            });

            if (addonForm) {
                addonForm.addEventListener('submit', (event) => {
                    event.preventDefault();

                    if (! activeMenu) {
                        closeAddonModal();
                        return;
                    }

                    const selected = [];

                    addonOptions.querySelectorAll('input[type="checkbox"]:checked').forEach((input) => {
                        selected.push({
                            id: Number.isNaN(Number(input.value)) ? input.value : Number(input.value),
                            name: input.dataset ? input.dataset.name : '',
                            extra_price: Number((input.dataset && input.dataset.price) ? input.dataset.price : 0),
                        });
                    });

                    const signature = signatureFor(activeMenu.id, selected);
                    const optionTotal = selected.reduce((sum, option) => sum + Number(option.extra_price || 0), 0);
                    const existing = cart.find((item) => item.signature === signature);

                    if (existing) {
                        existing.qty = Number(existing.qty || 0) + 1;
                    } else {
                        cart.push({
                            signature,
                            menu_id: activeMenu.id,
                            name: activeMenu.name,
                            price: Number(activeMenu.price),
                            options: selected,
                            optionTotal,
                            qty: 1,
                            notes: '',
                        });
                    }

                    persistCart();
                    showToast(`${activeMenu.name} ditambahkan ke keranjang`);
                    closeAddonModal();
                });
            }

            updateBadge();
        })();
    </script>
@endsection
