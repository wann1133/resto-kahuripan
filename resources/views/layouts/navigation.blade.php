<nav x-data="{ open: false }" class="sticky top-0 z-50 border-b border-white/40 bg-white/80 shadow-sm shadow-slate-900/5 backdrop-blur">
    @php
        $user = Auth::user();
        $role = $user?->role ?? 'guest';
        $initial = $user ? \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user->name, 0, 1)) : 'G';
    @endphp
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-10">
        <div class="flex items-center gap-6">
            <a href="{{ route('admin.dashboard') }}" class="group inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-100">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-600/10 text-emerald-600">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 2.5c-.437 0-.794.171-1.13.51L3.03 8.899a1.692 1.692 0 0 0 .4 2.608l5.577 3.271c.282.166.61.166.893 0l5.577-3.27a1.692 1.692 0 0 0 .398-2.61L11.13 3.01A1.585 1.585 0 0 0 10 2.5ZM5.5 12.5l4.5 2.643L14.5 12.5v3.125c0 .69-.56 1.25-1.25 1.25h-6.5c-.69 0-1.25-.56-1.25-1.25V12.5Z"/>
                    </svg>
                </span>
                <span>Resto Kahuripan</span>
            </a>
            <div class="hidden items-center gap-4 text-sm font-medium text-slate-500 sm:flex">
                @if ($role === 'admin')
                    <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        Dashboard
                    </x-nav-link>
                    <x-nav-link :href="route('menus.index')" :active="request()->routeIs('menus.*')">
                        Menu
                    </x-nav-link>
                    <x-nav-link :href="route('admin.tables.index')" :active="request()->routeIs('admin.tables.*')">
                        Meja
                    </x-nav-link>
                    <x-nav-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.*')">
                        Laporan
                    </x-nav-link>
                @endif

                @if (in_array($role, ['kitchen', 'admin'], true))
                    <x-nav-link :href="route('kitchen.index')" :active="request()->routeIs('kitchen.index')">
                        Dapur
                    </x-nav-link>
                @endif

                @if (in_array($role, ['cashier', 'admin'], true))
                    <x-nav-link :href="route('cashier.index')" :active="request()->routeIs('cashier.index')">
                        Kasir
                    </x-nav-link>
                @endif
            </div>
        </div>
        @if ($user)
            <div class="hidden items-center gap-4 sm:flex">
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $role }}</span>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 rounded-full border border-transparent bg-slate-900/5 px-3 py-2 text-sm font-medium text-slate-600 transition hover:border-slate-300 hover:bg-white hover:text-slate-800">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-900/10 text-slate-700">
                                {{ $initial }}
                            </span>
                            <div>{{ $user->name }}</div>
                            <div class="ms-1">
                                <svg class="h-4 w-4 fill-current text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Profil
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                Keluar
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        @endif
        @if ($user)
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-full p-2 text-slate-500 transition hover:bg-white hover:text-slate-700">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif
    </div>

    @if ($user)
        <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="space-y-1 border-t border-white/70 bg-white/90 px-4 py-3 backdrop-blur">
            @if ($role === 'admin')
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    Dashboard
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('menus.index')" :active="request()->routeIs('menus.*')">
                    Menu
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.tables.index')" :active="request()->routeIs('admin.tables.*')">
                    Meja
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.*')">
                    Laporan
                </x-responsive-nav-link>
            @endif
            @if (in_array($role, ['kitchen', 'admin'], true))
                <x-responsive-nav-link :href="route('kitchen.index')" :active="request()->routeIs('kitchen.index')">
                    Dapur
                </x-responsive-nav-link>
            @endif
            @if (in_array($role, ['cashier', 'admin'], true))
                <x-responsive-nav-link :href="route('cashier.index')" :active="request()->routeIs('cashier.index')">
                    Kasir
                </x-responsive-nav-link>
            @endif
        </div>
        <div class="border-t border-white/70 bg-white/90 px-4 py-4 backdrop-blur">
            <div class="text-sm font-semibold text-slate-700">{{ $user->name }}</div>
            <div class="text-xs uppercase text-slate-400">{{ $role }}</div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    Profil
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        Keluar
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    @endif
</nav>
