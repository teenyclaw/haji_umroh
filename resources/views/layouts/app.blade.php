<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') &middot; {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { brand: { 50:'#f0fdfa',100:'#ccfbf1',500:'#14b8a6',600:'#0d9488',700:'#0f766e',800:'#115e59' } }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full text-slate-800" x-data="{ sidebar: false }">
<div class="min-h-full">
    @include('layouts.partials.sidebar')

    <div class="lg:pl-64">
        <header class="sticky top-0 z-30 flex h-16 items-center gap-4 border-b border-slate-200 bg-white px-4 shadow-sm sm:px-6">
            <button @click="sidebar = !sidebar" class="lg:hidden text-slate-500">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="flex-1">
                <h1 class="text-lg font-semibold text-slate-800">@yield('heading', View::getSection('title') ?? 'Dashboard')</h1>
            </div>
            <div class="flex items-center gap-3" x-data="{ open: false }">
                @auth
                    @unless(auth()->user()->isSuperAdmin())
                        <a href="{{ route('portal.index', current_tenant()->slug) }}" target="_blank" class="hidden sm:inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-1.5 text-sm text-slate-600 hover:bg-slate-50">
                            Lihat Portal Publik
                        </a>
                    @endunless
                    <button @click="open = !open" class="flex items-center gap-2 rounded-lg px-2 py-1.5 hover:bg-slate-50">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-600 text-sm font-semibold text-white">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                        <span class="hidden text-left sm:block">
                            <span class="block text-sm font-medium text-slate-700">{{ auth()->user()->name }}</span>
                            <span class="block text-xs text-slate-400">{{ auth()->user()->getRoleNames()->first() ?? (auth()->user()->isSuperAdmin() ? 'Super Admin' : 'Pengguna') }}</span>
                        </span>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-cloak class="absolute right-4 top-14 w-48 rounded-lg border border-slate-200 bg-white py-1 shadow-lg">
                        @unless(auth()->user()->isSuperAdmin())
                            <a href="{{ route('app.settings.edit') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">Pengaturan Travel</a>
                        @endunless
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-rose-600 hover:bg-rose-50">Keluar</button>
                        </form>
                    </div>
                @endauth
            </div>
        </header>

        <main class="px-4 py-6 sm:px-6 lg:px-8">
            @include('layouts.partials.flash')
            @yield('content')
        </main>
    </div>
</div>
<style>[x-cloak]{display:none!important}</style>
@stack('scripts')
</body>
</html>
