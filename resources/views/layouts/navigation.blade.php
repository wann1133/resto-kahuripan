@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Str;

    $user = Auth::user();
    $role = $user?->role ?? 'guest';

    $navItems = [];

    $isActive = static function (array|string $patterns): bool {
        $patterns = is_array($patterns) ? $patterns : [$patterns];

        foreach ($patterns as $pattern) {
            if (request()->routeIs($pattern)) {
                return true;
            }
        }

        return false;
    };

    if ($role === 'admin') {
        $navItems[] = [
            'label' => 'Dashboard',
            'href' => route('admin.dashboard'),
            'icon' => 'home',
            'active' => $isActive('admin.dashboard'),
        ];
        $navItems[] = [
            'label' => 'Menu',
            'href' => route('menus.index'),
            'icon' => 'menu',
            'active' => $isActive('menus.*'),
        ];
        $navItems[] = [
            'label' => 'Meja',
            'href' => route('admin.tables.index'),
            'icon' => 'table',
            'active' => $isActive('admin.tables.*'),
        ];
        $navItems[] = [
            'label' => 'Laporan',
            'href' => route('admin.reports.index'),
            'icon' => 'report',
            'active' => $isActive('admin.reports.*'),
        ];
        $navItems[] = [
            'label' => 'Pengaturan',
            'href' => route('admin.settings.payment.edit'),
            'icon' => 'settings',
            'active' => $isActive('admin.settings.*'),
        ];
    }

    if (in_array($role, ['kitchen', 'admin'], true)) {
        $navItems[] = [
            'label' => 'Dapur',
            'href' => route('kitchen.index'),
            'icon' => 'kitchen',
            'active' => $isActive('kitchen.index'),
        ];
    }

    if (in_array($role, ['cashier', 'admin'], true)) {
        $navItems[] = [
            'label' => 'Kasir',
            'href' => route('cashier.index'),
            'icon' => 'cashier',
            'active' => $isActive('cashier.index'),
        ];
    }

    $initial = $user ? Str::upper(Str::substr($user->name, 0, 1)) : 'G';
@endphp

@if ($user)
    <aside class="sticky top-0 hidden h-screen w-72 flex-col border-r border-slate-200/70 bg-white/90 pb-6 shadow-sm shadow-slate-900/5 backdrop-blur lg:flex">
        <div class="flex flex-1 flex-col">
            <div class="flex items-center gap-3 px-6 pb-6 pt-8">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-500/10 text-emerald-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m9 21-5-4V9l8-6 8 6v8l-5 4" />
                            <path d="M9 21v-6a3 3 0 0 1 6 0v6" />
                        </svg>
                    </span>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-emerald-500">Resto</p>
                        <p class="text-base font-semibold text-slate-800">Kahuripan</p>
                    </div>
                </a>
            </div>

            <nav class="flex-1 space-y-1 px-4">
                @foreach ($navItems as $item)
                    <a href="{{ $item['href'] }}"
                        class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition
                            {{ $item['active'] ? 'bg-emerald-500 text-white shadow-sm shadow-emerald-500/30' : 'text-slate-500 hover:bg-emerald-50/80 hover:text-emerald-600' }}">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl
                            {{ $item['active'] ? 'bg-emerald-500/20 text-white' : 'bg-slate-100 text-emerald-600 group-hover:bg-emerald-500/10 group-hover:text-emerald-600' }}">
                            @switch($item['icon'])
                                @case('home')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 9.5 12 3l9 6.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1Z" />
                                    </svg>
                                    @break
                                @case('menu')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 5h18" />
                                        <path d="M3 10h18" />
                                        <path d="M3 15h12" />
                                        <path d="M3 20h6" />
                                    </svg>
                                    @break
                                @case('table')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 10h18" />
                                        <path d="m9 21 3-6 3 6" />
                                        <path d="m4 10 2 11" />
                                        <path d="m20 10-2 11" />
                                        <path d="m12 3 6 7H6z" />
                                    </svg>
                                    @break
                                @case('report')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 3h18v18H3z" />
                                        <path d="M7 14h2v2H7z" />
                                        <path d="M11 10h2v6h-2z" />
                                        <path d="M15 6h2v10h-2z" />
                                    </svg>
                                    @break
                                @case('kitchen')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M7 3v18" />
                                        <path d="M11 3h3a4 4 0 0 1 4 4 4 4 0 0 1-4 4h-3z" />
                                        <path d="M10 17h8" />
                                        <path d="M10 21h8" />
                                    </svg>
                                    @break
                                @case('cashier')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="2" y="7" width="20" height="14" rx="2" />
                                        <path d="M12 7V3" />
                                        <path d="M8 3h8" />
                                        <path d="M5 11h4" />
                                        <path d="M15 11h4" />
                                        <path d="M5 15h4" />
                                        <path d="M15 15h4" />
                                    </svg>
                                    @break
                                @case('settings')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="3" />
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33h.09a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51h.09a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82v.09a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z" />
                                    </svg>
                                    @break
                            @endswitch
                        </span>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="mt-6 px-6">
                <div class="rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Akun</p>
                    <div class="mt-3 flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-900/10 text-sm font-semibold text-slate-700">
                            {{ $initial }}
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-slate-700">{{ $user->name }}</p>
                            <p class="text-xs uppercase text-slate-400">{{ $role }}</p>
                        </div>
                    </div>
                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('profile.edit') }}" class="inline-flex flex-1 items-center justify-center rounded-full border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-500 transition hover:border-emerald-300 hover:text-emerald-600">
                            Profil
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-rose-500 px-3 py-1.5 text-xs font-semibold text-white shadow-sm shadow-rose-500/30 transition hover:bg-rose-600">
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 flex bg-slate-900/30 backdrop-blur lg:hidden" style="display: none;">
        <button type="button" class="flex-1" @click="sidebarOpen = false" aria-label="Tutup menu"></button>
        <div x-show="sidebarOpen"
            x-transition:enter="transition transform duration-200"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition transform duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="relative z-50 flex h-full w-80 flex-col bg-white shadow-xl">
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                <div class="flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-600">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m9 21-5-4V9l8-6 8 6v8l-5 4" />
                            <path d="M9 21v-6a3 3 0 0 1 6 0v6" />
                        </svg>
                    </span>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-emerald-500">Resto</p>
                        <p class="text-sm font-semibold text-slate-800">Kahuripan</p>
                    </div>
                </div>
                <button type="button" class="rounded-full p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700" @click="sidebarOpen = false">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m9 9 6 6" />
                        <path d="m15 9-6 6" />
                    </svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto px-6 py-6">
                <nav class="space-y-1">
                    @foreach ($navItems as $item)
                        <a href="{{ $item['href'] }}"
                            class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition
                                {{ $item['active'] ? 'bg-emerald-500 text-white shadow-sm shadow-emerald-500/30' : 'text-slate-500 hover:bg-emerald-50/80 hover:text-emerald-600' }}"
                            @click="sidebarOpen = false">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl
                                {{ $item['active'] ? 'bg-emerald-500/20 text-white' : 'bg-slate-100 text-emerald-600 group-hover:bg-emerald-500/10 group-hover:text-emerald-600' }}">
                                @switch($item['icon'])
                                    @case('home')
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 9.5 12 3l9 6.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1Z" />
                                        </svg>
                                        @break
                                    @case('menu')
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 5h18" />
                                            <path d="M3 10h18" />
                                            <path d="M3 15h12" />
                                            <path d="M3 20h6" />
                                        </svg>
                                        @break
                                    @case('table')
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 10h18" />
                                            <path d="m9 21 3-6 3 6" />
                                            <path d="m4 10 2 11" />
                                            <path d="m20 10-2 11" />
                                            <path d="m12 3 6 7H6z" />
                                        </svg>
                                        @break
                                    @case('report')
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 3h18v18H3z" />
                                            <path d="M7 14h2v2H7z" />
                                            <path d="M11 10h2v6h-2z" />
                                            <path d="M15 6h2v10h-2z" />
                                        </svg>
                                        @break
                                    @case('kitchen')
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M7 3v18" />
                                            <path d="M11 3h3a4 4 0 0 1 4 4 4 4 0 0 1-4 4h-3z" />
                                            <path d="M10 17h8" />
                                            <path d="M10 21h8" />
                                        </svg>
                                        @break
                                    @case('cashier')
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="2" y="7" width="20" height="14" rx="2" />
                                            <path d="M12 7V3" />
                                            <path d="M8 3h8" />
                                            <path d="M5 11h4" />
                                            <path d="M15 11h4" />
                                            <path d="M5 15h4" />
                                            <path d="M15 15h4" />
                                        </svg>
                                        @break
                                    @case('settings')
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="3" />
                                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33h.09a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51h.09a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82v.09a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z" />
                                        </svg>
                                        @break
                                @endswitch
                            </span>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </nav>
            </div>
            <div class="border-t border-slate-200 px-6 py-5">
                <div class="rounded-2xl border border-slate-200 bg-slate-50/80 px-4 py-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Akun</p>
                    <div class="mt-3 flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-900/10 text-sm font-semibold text-slate-700">
                            {{ $initial }}
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-slate-700">{{ $user->name }}</p>
                            <p class="text-xs uppercase text-slate-400">{{ $role }}</p>
                        </div>
                    </div>
                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('profile.edit') }}" class="inline-flex flex-1 items-center justify-center rounded-full border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-500 transition hover:border-emerald-300 hover:text-emerald-600" @click="sidebarOpen = false">
                            Profil
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-rose-500 px-3 py-1.5 text-xs font-semibold text-white shadow-sm shadow-rose-500/30 transition hover:bg-rose-600">
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
