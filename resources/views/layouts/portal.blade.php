<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $tenant->name) &middot; {{ $tenant->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>:root { --brand: {{ $tenant->brand_color ?? '#0d9488' }}; }</style>
</head>
<body class="h-full bg-slate-50 text-slate-800">
    <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/90 backdrop-blur">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
            <a href="{{ route('portal.index', $tenant->slug) }}" class="flex items-center gap-2">
                @if($tenant->logoUrl())
                    <img src="{{ $tenant->logoUrl() }}" class="h-9" alt="{{ $tenant->name }}">
                @else
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg font-bold text-white" style="background: var(--brand)">{{ strtoupper(substr($tenant->name, 0, 1)) }}</span>
                @endif
                <span class="font-semibold">{{ $tenant->name }}</span>
            </a>
            <nav class="flex items-center gap-4 text-sm">
                <a href="{{ route('portal.packages', $tenant->slug) }}" class="text-slate-600 hover:text-slate-900">Paket</a>
                <a href="#kontak" class="text-slate-600 hover:text-slate-900">Kontak</a>
                @if($tenant->whatsapp)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $tenant->whatsapp) }}" target="_blank" class="rounded-lg px-4 py-2 font-semibold text-white" style="background: var(--brand)">WhatsApp</a>
                @endif
            </nav>
        </div>
    </header>

    @if(session('success'))
        <div class="mx-auto mt-4 max-w-6xl px-4">
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
        </div>
    @endif

    <main>@yield('content')</main>

    <footer id="kontak" class="mt-16 border-t border-slate-200 bg-white">
        <div class="mx-auto grid max-w-6xl gap-6 px-4 py-10 sm:grid-cols-2">
            <div>
                <h3 class="font-semibold text-slate-800">{{ $tenant->name }}</h3>
                <p class="mt-2 text-sm text-slate-500">{{ $tenant->address }}{{ $tenant->city ? ', ' . $tenant->city : '' }}</p>
                @if($tenant->izin_number)<p class="mt-1 text-xs text-slate-400">{{ $tenant->izin_number }}</p>@endif
            </div>
            <div class="text-sm text-slate-600 sm:text-right">
                @if($tenant->phone)<p>Telepon: {{ $tenant->phone }}</p>@endif
                @if($tenant->email)<p>Email: {{ $tenant->email }}</p>@endif
            </div>
        </div>
        <div class="border-t border-slate-100 py-4 text-center text-xs text-slate-400">&copy; {{ date('Y') }} {{ $tenant->name }}</div>
    </footer>
</body>
</html>
