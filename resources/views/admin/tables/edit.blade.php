@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-emerald-500">Manajemen Meja</p>
                <h1 class="text-2xl font-semibold text-slate-800">Edit Meja</h1>
                <p class="text-sm text-slate-500">Perbarui nomor meja atau status layanan tanpa mengubah riwayat pesanan.</p>
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

        <form action="{{ route('admin.tables.update', $table) }}" method="POST" class="space-y-8 text-sm text-slate-600">
            @csrf
            @method('PUT')

            <section class="rounded-3xl border border-slate-200 bg-white/90 p-6 shadow-sm shadow-slate-900/5">
                <h2 class="text-lg font-semibold text-slate-800">Informasi Meja</h2>
                <p class="text-xs text-slate-500">Nomor meja tidak boleh sama dengan meja lain agar pemetaan tetap unik.</p>

                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <label class="space-y-2">
                        <span class="font-semibold text-slate-700">Nomor Meja</span>
                        <input type="text" name="number" value="{{ old('number', $table->number) }}" required
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-100">
                    </label>
                    <label class="flex items-center gap-2 self-end text-sm font-medium text-slate-600">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $table->is_active) ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        Aktifkan meja
                    </label>
                </div>
            </section>

            <section class="rounded-3xl border border-slate-200 bg-white/90 p-6 shadow-sm shadow-slate-900/5">
                <h2 class="text-lg font-semibold text-slate-800">QR Code</h2>
                <p class="text-xs text-slate-500">Bagikan atau cetak QR code berikut untuk ditempel di meja.</p>

                <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="rounded-3xl border border-dashed border-slate-200 bg-slate-50/70 p-6">
                        {!! QrCode::size(160)->generate(url('/t/'.$table->code)) !!}
                    </div>
                    <div class="space-y-3 text-xs text-slate-500">
                        <p>Kode: <span class="font-semibold text-slate-700">{{ $table->code }}</span></p>
                        <p>URL: <a href="{{ url('/t/'.$table->code) }}" class="text-emerald-600 hover:underline" target="_blank">{{ url('/t/'.$table->code) }}</a></p>
                        <form id="regenerate-qr" action="{{ route('admin.tables.regenerate', $table) }}" method="POST" class="space-y-3">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-2 rounded-full bg-amber-500 px-4 py-2 text-xs font-semibold text-white shadow-sm shadow-amber-500/30 transition hover:bg-amber-600">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="1 4 1 10 7 10"></polyline>
                                    <polyline points="23 20 23 14 17 14"></polyline>
                                    <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10"></path>
                                    <path d="M3.51 15A9 9 0 0 0 18.36 18.36L23 14"></path>
                                </svg>
                                Generate ulang QR
                            </button>
                        </form>
                    </div>
                </div>
            </section>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.tables.index') }}"
                    class="inline-flex items-center rounded-full border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-500 transition hover:border-slate-300 hover:text-slate-700">
                    Batal
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-full bg-emerald-500 px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-emerald-500/30 transition hover:bg-emerald-600">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection
