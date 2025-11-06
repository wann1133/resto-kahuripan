@extends('layouts.app')

@section('content')
    <div class="grid gap-8 lg:grid-cols-[1.1fr_minmax(360px,0.9fr)]">
        <section class="space-y-4">
            <header>
                <p class="text-xs uppercase tracking-[0.35em] text-emerald-500">Manajemen Meja</p>
                <h2 class="text-2xl font-semibold text-slate-800">Konfigurasi Area Dine-in</h2>
                <p class="text-sm text-slate-500">Kelola nomor, status, dan QR unik untuk tiap meja.</p>
            </header>

            <div class="space-y-4 text-sm text-slate-600">
                @foreach ($tables as $table)
                    <article class="rounded-3xl border border-slate-200/60 bg-white/90 p-6 shadow-sm shadow-slate-900/5">
                        <form id="update-table-{{ $table->id }}" action="{{ route('admin.tables.update', $table) }}" method="POST" class="grid gap-4 md:grid-cols-[1.4fr_minmax(140px,0.6fr)]">
                            @csrf
                            @method('PUT')
                            <label class="space-y-2">
                                <span class="text-sm font-semibold text-slate-700">Nomor Meja</span>
                                <input type="text" name="number" value="{{ $table->number }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-100">
                            </label>
                            <label class="flex items-center gap-2 text-sm font-medium text-slate-600">
                                <input type="checkbox" name="is_active" value="1" {{ $table->is_active ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                Aktif melayani
                            </label>
                        </form>

                        <div class="mt-4 flex flex-wrap items-center gap-3 text-xs uppercase tracking-wide text-slate-400">
                            <span>Kode: <span class="font-semibold text-slate-600">{{ $table->code }}</span></span>
                            <a href="{{ url('/t/'.$table->code) }}" class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 font-semibold text-emerald-600 transition hover:border-emerald-300 hover:bg-emerald-100" target="_blank">Pratinjau Halaman</a>
                        </div>

                        <div class="mt-6 flex flex-wrap items-center gap-3 text-sm">
                            <button form="{{ 'update-table-'.$table->id }}" class="inline-flex items-center rounded-full bg-emerald-500 px-4 py-2 font-semibold text-white transition hover:bg-emerald-600">Simpan</button>
                            <form action="{{ route('admin.tables.regenerate', $table) }}" method="POST">
                                @csrf
                                <button class="inline-flex items-center rounded-full bg-amber-500 px-4 py-2 font-semibold text-white transition hover:bg-amber-600" type="submit">Generate QR Baru</button>
                            </form>
                            <form action="{{ route('admin.tables.destroy', $table) }}" method="POST" onsubmit="return confirm('Hapus meja ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="inline-flex items-center rounded-full bg-rose-500 px-4 py-2 font-semibold text-white transition hover:bg-rose-600">Hapus</button>
                            </form>
                        </div>

                        <div class="mt-6 flex flex-col items-center gap-3 rounded-2xl border border-dashed border-slate-200 bg-slate-50/60 px-6 py-5 text-center">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">QR Digital</p>
                            <div>{!! QrCode::size(160)->generate(url('/t/'.$table->code)) !!}</div>
                            <p class="text-xs text-slate-400">Tempelkan pada meja agar pelanggan dapat memesan langsung.</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <aside class="rounded-3xl border border-slate-200/60 bg-white/90 p-6 shadow-sm shadow-slate-900/5">
            <h2 class="text-lg font-semibold text-slate-800">Tambah Meja</h2>
            <p class="mt-1 text-sm text-slate-500">Simak ketersediaan kursi dan aktifkan meja hanya saat siap digunakan.</p>
            <form action="{{ route('admin.tables.store') }}" method="POST" class="mt-6 space-y-4 text-sm text-slate-600">
                @csrf
                <label class="space-y-2">
                    <span class="font-semibold text-slate-700">Nomor Meja</span>
                    <input type="text" name="number" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-100" required>
                </label>
                <label class="flex items-center gap-2 text-sm font-medium text-slate-600">
                    <input type="checkbox" name="is_active" value="1" checked class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    Aktifkan setelah dibuat
                </label>
                <button class="w-full rounded-full bg-emerald-500 px-5 py-3 font-semibold text-white shadow-sm shadow-emerald-500/40 transition hover:bg-emerald-600">Simpan Meja</button>
            </form>
        </aside>
    </div>
@endsection
