@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
@endphp

@section('content')
    <div class="space-y-6">
        <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-emerald-500">Manajemen Menu</p>
                <h1 class="text-2xl font-semibold text-slate-800">Katalog Hidangan</h1>
                <p class="text-sm text-slate-500">Kelola harga, stok, dan status penayangan menu dengan tampilan tabel yang lebih ringkas.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('menus.create') }}"
                    class="inline-flex items-center gap-2 rounded-full bg-emerald-500 px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-emerald-500/30 transition hover:bg-emerald-600">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 5v14"></path>
                        <path d="M5 12h14"></path>
                    </svg>
                    Tambah Menu
                </a>
            </div>
        </header>

        <div class="overflow-hidden rounded-3xl border border-slate-200/70 bg-white/80 shadow-sm shadow-slate-900/5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200/70 text-sm text-slate-600">
                    <thead class="bg-slate-50/70 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th scope="col" class="whitespace-nowrap px-5 py-3 text-left font-semibold">Menu</th>
                            <th scope="col" class="whitespace-nowrap px-5 py-3 text-left font-semibold">Kategori</th>
                            <th scope="col" class="whitespace-nowrap px-5 py-3 text-left font-semibold">Harga</th>
                            <th scope="col" class="whitespace-nowrap px-5 py-3 text-left font-semibold">Stok</th>
                            <th scope="col" class="whitespace-nowrap px-5 py-3 text-left font-semibold">Addons</th>
                            <th scope="col" class="whitespace-nowrap px-5 py-3 text-left font-semibold">Status</th>
                            <th scope="col" class="whitespace-nowrap px-5 py-3 text-left font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($menus as $menu)
                            @php
                                $imagePreview = null;
                                if ($menu->image_url) {
                                    $imagePreview = str_starts_with($menu->image_url, 'http')
                                        ? $menu->image_url
                                        : Storage::url($menu->image_url);
                                }
                            @endphp
                            <tr class="transition-colors hover:bg-slate-50/70">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="h-16 w-16 overflow-hidden rounded-2xl border border-slate-200 bg-slate-100 shadow-sm">
                                            @if ($imagePreview)
                                                <img src="{{ $imagePreview }}" alt="{{ $menu->name }}" class="h-full w-full object-cover">
                                            @else
                                                <div class="flex h-full w-full items-center justify-center text-xs font-medium text-slate-400">
                                                    Tidak ada gambar
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-800">{{ $menu->name }}</p>
                                            <p class="mt-1 text-xs leading-relaxed text-slate-500">
                                                {{ $menu->description ? Str::limit($menu->description, 90) : 'Belum ada deskripsi.' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-slate-600">{{ $menu->category }}</td>
                                <td class="px-5 py-4 font-semibold text-slate-700">Rp {{ number_format($menu->price, 0, ',', '.') }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $menu->stock }}</td>
                                <td class="px-5 py-4">
                                    @if ($menu->options->isEmpty())
                                        <span class="rounded-full border border-slate-200 px-3 py-1 text-xs text-slate-400">Tidak ada addons</span>
                                    @else
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($menu->options as $option)
                                                <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-600">
                                                    {{ $option->name }}
                                                    @if ($option->extra_price)
                                                        <span class="text-emerald-500">+Rp {{ number_format($option->extra_price, 0, ',', '.') }}</span>
                                                    @endif
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-wide
                                        {{ $menu->is_active ? 'border-emerald-200 bg-emerald-50 text-emerald-600' : 'border-slate-200 bg-slate-50 text-slate-500' }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $menu->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        {{ $menu->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <a href="{{ route('menus.edit', $menu) }}"
                                            class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 transition hover:border-emerald-300 hover:text-emerald-600">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path d="M12 20h9"></path>
                                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path>
                                            </svg>
                                            Edit
                                        </a>
                                        <form id="delete-menu-{{ $menu->id }}" action="{{ route('menus.destroy', $menu) }}" method="POST" class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        <button type="button" data-trigger="delete" data-form="delete-menu-{{ $menu->id }}"
                                            data-name="menu {{ $menu->name }}"
                                            class="inline-flex items-center gap-2 rounded-full bg-rose-500 px-4 py-2 text-xs font-semibold text-white shadow-sm shadow-rose-500/30 transition hover:bg-rose-600">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path d="M3 6h18"></path>
                                                <path d="M8 6v14c0 .55.45 1 1 1h6c.55 0 1-.45 1-1V6"></path>
                                                <path d="M10 11v6"></path>
                                                <path d="M14 11v6"></path>
                                                <path d="M9 6l1-3h4l1 3"></path>
                                            </svg>
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-sm text-slate-500">
                                    Belum ada menu yang terdaftar. Mulai dengan menambahkan menu baru.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <dialog id="confirm-delete-modal" class="w-full max-w-md rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl shadow-slate-900/20">
        <div class="space-y-4 text-sm text-slate-600">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-rose-50 text-rose-500">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path>
                        <line x1="18" y1="9" x2="12" y2="15"></line>
                        <line x1="12" y1="9" x2="18" y2="15"></line>
                    </svg>
                </span>
                <div>
                    <h2 class="text-base font-semibold text-slate-800">Konfirmasi Hapus</h2>
                    <p id="delete-message" class="text-xs text-slate-500"></p>
                </div>
            </div>
            <p class="text-xs text-slate-500">Tindakan ini tidak dapat dibatalkan. Data menu yang dihapus tidak akan tampil lagi pada pelanggan.</p>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" data-action="cancel"
                    class="inline-flex items-center rounded-full border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-500 transition hover:border-slate-300 hover:text-slate-700">
                    Batal
                </button>
                <button type="button" data-action="confirm"
                    class="inline-flex items-center rounded-full bg-rose-500 px-4 py-2 text-xs font-semibold text-white shadow-sm shadow-rose-500/30 transition hover:bg-rose-600">
                    Ya, hapus
                </button>
            </div>
        </div>
    </dialog>

    @once
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const dialog = document.getElementById('confirm-delete-modal');
                const message = document.getElementById('delete-message');
                let pendingForm = null;

                function fallbackConfirm(text, form) {
                    if (window.confirm(text)) {
                        form.submit();
                    }
                }

                document.querySelectorAll('[data-trigger="delete"]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const formId = button.getAttribute('data-form');
                        const name = button.getAttribute('data-name') || 'item ini';
                        const targetForm = document.getElementById(formId);

                        if (! targetForm) {
                            return;
                        }

                        const confirmationText = `Anda yakin ingin menghapus ${name}?`;

                        if (! dialog || typeof dialog.showModal !== 'function') {
                            fallbackConfirm(confirmationText, targetForm);
                            return;
                        }

                        pendingForm = targetForm;
                        message.textContent = confirmationText;
                        dialog.showModal();
                    });
                });

                if (dialog) {
                    dialog.addEventListener('cancel', (event) => {
                        event.preventDefault();
                        dialog.close();
                    });

                    dialog.querySelector('[data-action="cancel"]')?.addEventListener('click', () => {
                        pendingForm = null;
                        dialog.close();
                    });

                    dialog.querySelector('[data-action="confirm"]')?.addEventListener('click', () => {
                        if (pendingForm) {
                            pendingForm.submit();
                            pendingForm = null;
                        }
                        dialog.close();
                    });
                }
            });
        </script>
    @endonce
@endsection
