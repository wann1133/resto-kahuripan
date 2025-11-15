@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-emerald-500">Manajemen Meja</p>
                <h1 class="text-2xl font-semibold text-slate-800">Konfigurasi Area Dine-in</h1>
                <p class="text-sm text-slate-500">Kelola nomor meja, status layanan, dan QR code tamu melalui tampilan tabel.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.tables.create') }}"
                    class="inline-flex items-center gap-2 rounded-full bg-emerald-500 px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-emerald-500/30 transition hover:bg-emerald-600">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 5v14"></path>
                        <path d="M5 12h14"></path>
                    </svg>
                    Tambah Meja
                </a>
            </div>
        </header>

        <div class="overflow-hidden rounded-3xl border border-slate-200/70 bg-white/80 shadow-sm shadow-slate-900/5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200/70 text-sm text-slate-600">
                    <thead class="bg-slate-50/70 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th scope="col" class="whitespace-nowrap px-5 py-3 text-left font-semibold">Nomor</th>
                            <th scope="col" class="whitespace-nowrap px-5 py-3 text-left font-semibold">Kode QR</th>
                            <th scope="col" class="whitespace-nowrap px-5 py-3 text-left font-semibold">Status</th>
                            <th scope="col" class="whitespace-nowrap px-5 py-3 text-left font-semibold">Pratinjau</th>
                            <th scope="col" class="whitespace-nowrap px-5 py-3 text-left font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($tables as $table)
                            <tr class="transition hover:bg-slate-50/70">
                                <td class="px-5 py-4 font-semibold text-slate-800">{{ $table->number }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-xs font-semibold uppercase tracking-wide text-slate-400">Kode</span>
                                        <span class="text-sm text-slate-700">{{ $table->code }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-wide
                                        {{ $table->is_active ? 'border-emerald-200 bg-emerald-50 text-emerald-600' : 'border-slate-200 bg-slate-50 text-slate-500' }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $table->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        {{ $table->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50/70 p-2">
                                            {!! QrCode::size(64)->generate(url('/t/'.$table->code)) !!}
                                        </div>
                                        <div class="flex flex-col gap-2 text-xs font-semibold sm:flex-row sm:flex-wrap">
                                            <a href="{{ url('/t/'.$table->code) }}" target="_blank"
                                                class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-3 py-1 text-slate-600 transition hover:border-emerald-300 hover:text-emerald-600">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                                    <polyline points="15 3 21 3 21 9"></polyline>
                                                    <line x1="10" y1="14" x2="21" y2="3"></line>
                                                </svg>
                                                Buka halaman
                                            </a>
                                            <a href="{{ route('admin.tables.download', $table) }}"
                                                class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-3 py-1 text-slate-600 transition hover:border-sky-300 hover:text-sky-600">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                    <polyline points="7 10 12 15 17 10"></polyline>
                                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                                </svg>
                                                Download QR
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <a href="{{ route('admin.tables.edit', $table) }}"
                                            class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 transition hover:border-emerald-300 hover:text-emerald-600">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path d="M12 20h9"></path>
                                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path>
                                            </svg>
                                            Edit
                                        </a>
                                        <form id="regenerate-qr-{{ $table->id }}" action="{{ route('admin.tables.regenerate', $table) }}" method="POST" class="hidden">
                                            @csrf
                                        </form>
                                        <button type="button"
                                            class="inline-flex items-center gap-2 rounded-full bg-amber-500 px-4 py-2 text-xs font-semibold text-white shadow-sm shadow-amber-500/30 transition hover:bg-amber-600"
                                            onclick="document.getElementById('regenerate-qr-{{ $table->id }}').submit()">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <polyline points="1 4 1 10 7 10"></polyline>
                                                <polyline points="23 20 23 14 17 14"></polyline>
                                                <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10"></path>
                                                <path d="M3.51 15A9 9 0 0 0 18.36 18.36L23 14"></path>
                                            </svg>
                                            QR baru
                                        </button>
                                        <form id="delete-table-{{ $table->id }}" action="{{ route('admin.tables.destroy', $table) }}" method="POST" class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        <button type="button" data-trigger="delete" data-form="delete-table-{{ $table->id }}"
                                            data-name="meja {{ $table->number }}"
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
                                <td colspan="5" class="px-5 py-12 text-center text-sm text-slate-500">
                                    Belum ada meja terdaftar. Tambahkan meja pertama Anda untuk mulai menerima pesanan.
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
            <p class="text-xs text-slate-500">Hapus meja hanya jika sudah tidak digunakan. QR code yang terkait juga akan dinonaktifkan.</p>
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
