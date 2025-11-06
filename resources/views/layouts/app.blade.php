<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Resto Kahuripan') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Shared layout for authenticated areas -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body x-data="{ sidebarOpen: false }" class="min-h-screen bg-gradient-to-br from-slate-100 via-white to-slate-200 text-slate-900 antialiased">
        <div class="flex min-h-screen">
            @auth
                @include('layouts.navigation')
            @endauth

            <div class="flex flex-1 flex-col">
                @auth
                    @php
                        $user = Auth::user();
                        $initial = $user ? \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user->name, 0, 1)) : 'G';
                        $role = $user?->role ?? 'guest';
                    @endphp
                    <header class="sticky top-0 z-30 border-b border-white/60 bg-white/80 backdrop-blur">
                        <div class="flex items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                            <div class="flex items-center gap-3">
                                <button type="button" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white p-2 text-slate-600 shadow-sm transition hover:border-emerald-300 hover:text-emerald-600 lg:hidden" @click="sidebarOpen = true" aria-label="Buka menu">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 6h16" />
                                        <path d="M4 12h16" />
                                        <path d="M4 18h16" />
                                    </svg>
                                </button>
                                <div class="hidden lg:flex lg:flex-col">
                                    <span class="text-xs font-semibold uppercase tracking-[0.35em] text-emerald-500">Panel Admin</span>
                                    <span class="text-sm font-semibold text-slate-800">{{ config('app.name', 'Resto Kahuripan') }}</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <span class="hidden rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-500 lg:inline-flex">{{ $role }}</span>

                                <x-dropdown align="right" width="48">
                                    <x-slot name="trigger">
                                        <button class="inline-flex items-center gap-2 rounded-full border border-transparent bg-slate-900/5 px-3 py-2 text-sm font-medium text-slate-600 transition hover:border-slate-300 hover:bg-white hover:text-slate-800">
                                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-900/10 text-slate-700">
                                                {{ $initial }}
                                            </span>
                                            <div class="hidden text-left leading-tight sm:block">
                                                <p class="text-sm font-semibold text-slate-700">{{ $user->name }}</p>
                                                <p class="text-xs uppercase text-slate-400">{{ $role }}</p>
                                            </div>
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
                        </div>
                    </header>
                @endauth

                @isset($header)
                    <div class="border-b border-slate-200/80 bg-white/70 backdrop-blur">
                        <div class="mx-auto flex w-full max-w-7xl flex-col gap-2 px-4 py-6 sm:px-6 lg:px-10">
                            {{ $header }}
                        </div>
                    </div>
                @endisset

                <main class="flex-1">
                    <div class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-10">
                        <section class="space-y-4">
                            @if (session('success'))
                                <div class="rounded-xl border border-emerald-100 bg-emerald-50/70 px-4 py-3 text-sm text-emerald-700 shadow-sm shadow-emerald-100">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if (session('status'))
                                <div class="rounded-xl border border-sky-100 bg-sky-50/70 px-4 py-3 text-sm text-sky-700 shadow-sm shadow-sky-100">
                                    {{ session('status') }}
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="rounded-xl border border-rose-100 bg-rose-50/70 px-4 py-3 text-sm text-rose-700 shadow-sm shadow-rose-100">
                                    <ul class="list-disc space-y-1 pl-5">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="rounded-3xl border border-white/60 bg-white/80 p-6 shadow-lg shadow-slate-900/5 backdrop-blur">
                                {{ $slot ?? '' }}
                                @yield('content')
                            </div>
                        </section>
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
