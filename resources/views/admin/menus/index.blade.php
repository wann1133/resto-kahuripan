@extends('layouts.app')

@section('content')
    <div class="grid gap-8 xl:grid-cols-[1.1fr_minmax(420px,0.9fr)]">
        <section class="space-y-4">
            <header>
                <p class="text-xs uppercase tracking-[0.35em] text-emerald-500">Manajemen Menu</p>
                <h2 class="text-2xl font-semibold text-slate-800">Katalog Hidangan</h2>
                <p class="text-sm text-slate-500">Perbarui menu dan stok agar pelanggan selalu melihat informasi terbaru.</p>
            </header>

            <div class="space-y-4">
                @foreach ($menus as $menu)
                    <article class="rounded-3xl border border-slate-200/60 bg-white/90 p-6 shadow-sm shadow-slate-900/5">
                        <form id="update-menu-{{ $menu->id }}" action="{{ route('menus.update', $menu) }}" method="POST" class="grid gap-4 text-sm text-slate-600 md:grid-cols-2">
                            @csrf
                            @method('PUT')
                            <label class="space-y-2">
                                <span class="font-semibold text-slate-700">Nama Menu</span>
                                <input type="text" name="name" value="{{ $menu->name }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-100">
                            </label>
                            <label class="space-y-2">
                                <span class="font-semibold text-slate-700">Kategori</span>
                                <input type="text" name="category" value="{{ $menu->category }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-100">
                            </label>
                            <label class="space-y-2">
                                <span class="font-semibold text-slate-700">Harga</span>
                                <input type="number" name="price" value="{{ $menu->price }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-100">
                            </label>
                            <label class="space-y-2">
                                <span class="font-semibold text-slate-700">Stok</span>
                                <input type="number" name="stock" value="{{ $menu->stock }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-100">
                            </label>
                            <label class="md:col-span-2 space-y-2">
                                <span class="font-semibold text-slate-700">Deskripsi</span>
                                <textarea name="description" rows="2" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-100">{{ $menu->description }}</textarea>
                            </label>
                            <label class="space-y-2">
                                <span class="font-semibold text-slate-700">URL Gambar</span>
                                <input type="text" name="image_url" value="{{ $menu->image_url }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-100">
                            </label>
                            <label class="flex items-center gap-2 text-sm font-medium text-slate-600">
                                <input type="checkbox" name="is_active" value="1" {{ $menu->is_active ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                Tampilkan di menu digital
                            </label>
                        </form>

                        @if ($menu->options->isNotEmpty())
                            <div class="mt-4 flex flex-wrap gap-2 text-xs text-slate-500">
                                @foreach ($menu->options as $option)
                                    <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1">
                                        {{ $option->name }}
                                        @if ($option->extra_price)
                                            <span class="text-emerald-600">+Rp {{ number_format($option->extra_price, 0, ',', '.') }}</span>
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        <div class="mt-6 flex flex-wrap items-center gap-3 text-sm">
                            <button form="{{ 'update-menu-'.$menu->id }}" class="inline-flex items-center rounded-full bg-emerald-500 px-4 py-2 font-semibold text-white transition hover:bg-emerald-600">Simpan Perubahan</button>
                            <form action="{{ route('menus.destroy', $menu) }}" method="POST" onsubmit="return confirm('Hapus menu ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="inline-flex items-center rounded-full bg-rose-500 px-4 py-2 font-semibold text-white transition hover:bg-rose-600">Hapus</button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <aside class="rounded-3xl border border-slate-200/60 bg-white/90 p-6 shadow-sm shadow-slate-900/5">
            <h2 class="text-lg font-semibold text-slate-800">Tambah Menu Baru</h2>
            <p class="mt-1 text-sm text-slate-500">Lengkapi data hidangan untuk langsung tampil di halaman pelanggan.</p>
            <form action="{{ route('menus.store') }}" method="POST" class="mt-6 space-y-4 text-sm text-slate-600">
                @csrf
                <label class="space-y-2">
                    <span class="font-semibold text-slate-700">Nama Menu</span>
                    <input type="text" name="name" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-100" required>
                </label>
                <label class="space-y-2">
                    <span class="font-semibold text-slate-700">Kategori</span>
                    <input type="text" name="category" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-100" required>
                </label>
                <label class="space-y-2">
                    <span class="font-semibold text-slate-700">Deskripsi</span>
                    <textarea name="description" rows="3" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-100"></textarea>
                </label>
                <label class="space-y-2">
                    <span class="font-semibold text-slate-700">Harga</span>
                    <input type="number" name="price" min="0" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-100" required>
                </label>
                <label class="space-y-2">
                    <span class="font-semibold text-slate-700">Stok</span>
                    <input type="number" name="stock" min="0" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-100" required>
                </label>
                <label class="flex items-center gap-2 text-sm font-medium text-slate-600">
                    <input type="checkbox" name="is_active" value="1" checked class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    Aktifkan menu setelah disimpan
                </label>
                <label class="space-y-2">
                    <span class="font-semibold text-slate-700">URL Gambar (opsional)</span>
                    <input type="text" name="image_url" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-100">
                </label>
                <button class="w-full rounded-full bg-emerald-500 px-5 py-3 font-semibold text-white shadow-sm shadow-emerald-500/40 transition hover:bg-emerald-600">Simpan Menu</button>
            </form>
        </aside>
    </div>
@endsection
