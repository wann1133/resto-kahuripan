@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-emerald-500">Manajemen Meja</p>
                <h1 class="text-2xl font-semibold text-slate-800">Tambah Meja</h1>
                <p class="text-sm text-slate-500">Daftarkan meja baru agar pelanggan dapat memindai QR code dan memesan.</p>
            </div>
            <a href="{{ route('admin.tables.index') }}"
                class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-800">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M15 18l-6-6 6-6"></path>
                </svg>
                Kembali ke daftar
            </a>
        </div>

        <form action="{{ route('admin.tables.store') }}" method="POST" class="space-y-8 text-sm text-slate-600">
            @csrf

            <section class="rounded-3xl border border-slate-200 bg-white/90 p-6 shadow-sm shadow-slate-900/5">
                <h2 class="text-lg font-semibold text-slate-800">Informasi Meja</h2>
                <p class="text-xs text-slate-500">Nomor meja akan digunakan pada setiap QR code.</p>

                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <label class="space-y-2">
                        <span class="font-semibold text-slate-700">Nomor Meja</span>
                        <input type="text" name="number" value="{{ old('number') }}" required
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-100">
                    </label>
                    <label class="flex items-center gap-2 self-end text-sm font-medium text-slate-600">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        Aktifkan meja setelah dibuat
                    </label>
                </div>
            </section>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.tables.index') }}"
                    class="inline-flex items-center rounded-full border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-500 transition hover:border-slate-300 hover:text-slate-700">
                    Batal
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-full bg-emerald-500 px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-emerald-500/30 transition hover:bg-emerald-600">
                    Simpan Meja
                </button>
            </div>
        </form>
    </div>
@endsection
