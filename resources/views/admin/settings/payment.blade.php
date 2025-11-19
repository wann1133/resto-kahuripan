@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
    <div class="space-y-6">
        <header class="flex flex-col gap-2">
            <p class="text-xs uppercase tracking-[0.35em] text-emerald-500">Pembayaran</p>
            <h1 class="text-2xl font-semibold text-slate-800">Pengaturan QRIS</h1>
            <p class="text-sm text-slate-500">Unggah QR statis dari penyedia lalu sistem akan membentuk QRIS dinamis dengan nominal setiap transaksi.</p>
        </header>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1.5fr)_minmax(0,1fr)]">
            <form action="{{ route('admin.settings.payment.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6 rounded-3xl border border-slate-200/80 bg-white/90 p-6 shadow-sm shadow-slate-900/5">
                @csrf
                @method('PUT')

                <div class="space-y-2">
                    <label for="qris_payload" class="text-sm font-semibold text-slate-800">Payload QRIS Statis</label>
                    <textarea id="qris_payload" name="qris_payload" rows="6" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-100" placeholder="000201010212...">{{ old('qris_payload', $qrisPayload) }}</textarea>
                    <p class="text-xs text-slate-500">Tempel data mentah dari QR statis (biasanya dimulai dengan 000201). Sistem akan mengganti field nominal (tag 54) dan CRC (tag 63) secara otomatis.</p>
                </div>

                <div class="space-y-2">
                    <label for="qris_image" class="text-sm font-semibold text-slate-800">File QR Statis (opsional)</label>
                    <input type="file" id="qris_image" name="qris_image" accept="image/*" class="w-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                    <p class="text-xs text-slate-500">Gambar ini hanya untuk pratinjau pada kasir/pelanggan jika dibutuhkan.</p>
                    @if ($qrisImage)
                        <div class="mt-3 flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50/70 p-3">
                            <img src="{{ Storage::url($qrisImage) }}" alt="QRIS" class="h-20 w-20 rounded-xl border border-slate-200 object-cover">
                            <label class="flex items-center gap-2 text-xs text-slate-500">
                                <input type="checkbox" name="remove_qris_image" value="1" class="rounded border-slate-300 text-emerald-500 focus:ring-emerald-500">
                                Hapus gambar saat simpan
                            </label>
                        </div>
                    @endif
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-full bg-emerald-500 px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-emerald-500/40 transition hover:bg-emerald-600">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14" />
                            <path d="M12 5v14" />
                        </svg>
                        Simpan
                    </button>
                </div>
            </form>

            <div class="space-y-4 rounded-3xl border border-slate-200/80 bg-white/90 p-6 shadow-sm shadow-slate-900/5">
                <h2 class="text-lg font-semibold text-slate-800">Cara mendapatkan payload</h2>
                <ol class="list-decimal space-y-2 pl-5 text-sm text-slate-600">
                    <li>Ambil gambar QR statis resmi dari penyedia QRIS.</li>
                    <li>Gunakan scanner pihak ketiga (mis. aplikasi seluler/website) untuk mengekstrak data mentah (string 000201...).</li>
                    <li>Tempel string tersebut pada kolom Payload di samping.</li>
                    <li>Jika ingin menampilkan QR asli sebagai referensi, unggah file pada kolom Opsional.</li>
                </ol>
                <div class="rounded-2xl bg-emerald-50/60 p-4 text-xs text-emerald-700">
                    QR dinamis akan otomatis digenerate di halaman checkout pelanggan, sehingga nominal pembayaran terisi sebelum diproses aplikasi pembayaran.
                </div>
            </div>
        </div>
    </div>
@endsection
