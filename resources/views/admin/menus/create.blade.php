@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-emerald-500">Manajemen Menu</p>
                <h1 class="text-2xl font-semibold text-slate-800">Tambah Menu</h1>
                <p class="text-sm text-slate-500">Isi detail hidangan baru dan atur addons tambahan jika diperlukan.</p>
            </div>
            <a href="{{ route('menus.index') }}"
                class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-800">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M15 18l-6-6 6-6"></path>
                </svg>
                Kembali ke daftar
            </a>
        </div>

        <form action="{{ route('menus.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8 text-sm text-slate-600">
            @csrf
            <section class="rounded-3xl border border-slate-200 bg-white/90 p-6 shadow-sm shadow-slate-900/5">
                <h2 class="text-lg font-semibold text-slate-800">Informasi Utama</h2>
                <p class="text-xs text-slate-500">Nama, kategori, dan stok untuk menampilkan menu pada pelanggan.</p>

                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <label class="space-y-2">
                        <span class="font-semibold text-slate-700">Nama Menu</span>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-100">
                    </label>
                    <label class="space-y-2">
                        <span class="font-semibold text-slate-700">Kategori</span>
                        <input type="text" name="category" value="{{ old('category') }}" required
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-100">
                    </label>
                    <label class="space-y-2">
                        <span class="font-semibold text-slate-700">Harga</span>
                        <input type="number" name="price" min="0" step="100" value="{{ old('price') }}" required
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-100">
                    </label>
                    <label class="space-y-2">
                        <span class="font-semibold text-slate-700">Stok</span>
                        <input type="number" name="stock" min="0" value="{{ old('stock') }}" required
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-100">
                    </label>
                    <label class="md:col-span-2 space-y-2">
                        <span class="font-semibold text-slate-700">Deskripsi</span>
                        <textarea name="description" rows="4"
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-100">{{ old('description') }}</textarea>
                    </label>
                    <label class="space-y-2">
                        <span class="font-semibold text-slate-700">Unggah Foto Menu</span>
                        <input type="file" name="image" accept="image/*"
                            class="block w-full cursor-pointer rounded-2xl border border-dashed border-emerald-300 bg-emerald-50 px-4 py-4 text-xs font-medium text-emerald-600 file:me-4 file:rounded-full file:border-0 file:bg-emerald-500 file:px-4 file:py-2 file:text-white file:font-semibold focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100">
                        <span class="block text-xs text-slate-400">Format JPG/PNG, maksimal 3MB.</span>
                    </label>
                    <label class="flex items-center gap-2 self-end text-sm font-medium text-slate-600">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        Tampilkan di menu digital
                    </label>
                </div>
            </section>

            <section class="rounded-3xl border border-slate-200 bg-white/90 p-6 shadow-sm shadow-slate-900/5">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-800">Addons Menu</h2>
                        <p class="text-xs text-slate-500">Tambah pilihan tambahan agar pelanggan bisa menyesuaikan pesanan.</p>
                    </div>
                    <button type="button" id="add-addon-button"
                        class="inline-flex items-center gap-2 rounded-full border border-emerald-300 bg-emerald-50 px-4 py-2 text-xs font-semibold text-emerald-600 transition hover:border-emerald-400 hover:bg-emerald-100">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 5v14"></path>
                            <path d="M5 12h14"></path>
                        </svg>
                        Tambah addon
                    </button>
                </div>

                <div id="addon-container" class="mt-6 space-y-4">
                    <p class="empty-state text-xs text-slate-400">Belum ada addons. Klik tombol di atas untuk menambah.</p>
                </div>
            </section>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('menus.index') }}"
                    class="inline-flex items-center rounded-full border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-500 transition hover:border-slate-300 hover:text-slate-700">
                    Batal
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-full bg-emerald-500 px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-emerald-500/30 transition hover:bg-emerald-600">
                    Simpan Menu
                </button>
            </div>
        </form>
    </div>

    @once
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const container = document.getElementById('addon-container');
                const addButton = document.getElementById('add-addon-button');
                let index = 0;

                function updateEmptyState() {
                    const hasField = container.querySelectorAll('.addon-row').length > 0;
                    container.querySelector('.empty-state')?.classList.toggle('hidden', hasField);
                    if (! hasField && ! container.querySelector('.empty-state')) {
                        const emptyP = document.createElement('p');
                        emptyP.className = 'empty-state text-xs text-slate-400';
                        emptyP.textContent = 'Belum ada addons. Klik tombol di atas untuk menambah.';
                        container.appendChild(emptyP);
                    }
                }

                function buildAddonRow() {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'addon-row grid gap-3 rounded-2xl border border-slate-200 bg-slate-50/80 p-4 md:grid-cols-[1.4fr_minmax(120px,0.6fr)_auto]';

                    wrapper.innerHTML = `
                        <label class="space-y-1">
                            <span class="text-xs font-semibold text-slate-500">Nama addon</span>
                            <input type="text" name="options[${index}][name]" required
                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-100">
                        </label>
                        <label class="space-y-1">
                            <span class="text-xs font-semibold text-slate-500">Biaya tambahan</span>
                            <input type="number" name="options[${index}][extra_price]" min="0" step="100"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-100">
                        </label>
                        <button type="button" class="remove-addon inline-flex h-10 w-10 items-center justify-center rounded-xl bg-rose-100 text-rose-500 transition hover:bg-rose-200">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M18 6L6 18"></path>
                                <path d="M6 6l12 12"></path>
                            </svg>
                        </button>
                    `;

                    wrapper.querySelector('.remove-addon').addEventListener('click', () => {
                        wrapper.remove();
                        updateEmptyState();
                    });

                    index += 1;
                    return wrapper;
                }

                addButton?.addEventListener('click', () => {
                    const row = buildAddonRow();
                    container.appendChild(row);
                    updateEmptyState();
                });

                updateEmptyState();
            });
        </script>
    @endonce
@endsection
