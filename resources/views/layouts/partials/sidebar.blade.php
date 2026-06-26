@php
    $isSuper = auth()->user()?->isSuperAdmin();

    $tenantNav = [
        ['label' => 'Dashboard', 'route' => 'app.dashboard', 'pattern' => 'app.dashboard'],
        ['label' => 'Paket', 'route' => 'app.packages.index', 'pattern' => 'app.packages.*'],
        ['label' => 'Jamaah', 'route' => 'app.jamaah.index', 'pattern' => 'app.jamaah.*'],
        ['label' => 'Booking', 'route' => 'app.bookings.index', 'pattern' => 'app.bookings.*'],
        ['label' => 'Invoice', 'route' => 'app.invoices.index', 'pattern' => 'app.invoices.*'],
        ['label' => 'Pembayaran', 'route' => 'app.payments.index', 'pattern' => ['app.payments.index', 'app.payments.create', 'app.payments.store', 'app.payments.verify', 'app.payments.reject', 'app.payments.receipt']],
        ['label' => 'Kasir', 'route' => 'app.payments.cashier', 'pattern' => ['app.payments.cashier', 'app.payments.cashier.store', 'app.payments.cashier.success']],
        ['label' => 'Dokumen', 'route' => 'app.documents.index', 'pattern' => 'app.documents.*'],
        ['label' => 'Rombongan', 'route' => 'app.rombongan.index', 'pattern' => 'app.rombongan.*'],
        ['label' => 'Manasik', 'route' => 'app.manasik.index', 'pattern' => 'app.manasik.*'],
        ['label' => 'Agent', 'route' => 'app.agents.index', 'pattern' => 'app.agents.*'],
        ['label' => 'Komisi', 'route' => 'app.commissions.index', 'pattern' => 'app.commissions.*'],
        ['label' => 'Inquiry', 'route' => 'app.inquiries.index', 'pattern' => 'app.inquiries.*'],
        ['label' => 'Laporan', 'route' => 'app.reports.index', 'pattern' => 'app.reports.*'],
        ['label' => 'Jenis Dokumen', 'route' => 'app.document-types.index', 'pattern' => 'app.document-types.*'],
        ['label' => 'Pengaturan', 'route' => 'app.settings.edit', 'pattern' => 'app.settings.*'],
    ];

    $adminNav = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'pattern' => 'admin.dashboard'],
        ['label' => 'Travel / Tenant', 'route' => 'admin.tenants.index', 'pattern' => 'admin.tenants.*'],
    ];

    $nav = $isSuper ? $adminNav : $tenantNav;
@endphp

<div x-show="sidebar" x-cloak @click="sidebar = false" class="fixed inset-0 z-40 bg-slate-900/50 lg:hidden"></div>

<aside
    class="fixed inset-y-0 left-0 z-50 w-64 transform overflow-y-auto bg-slate-900 transition-transform lg:translate-x-0"
    :class="sidebar ? 'translate-x-0' : '-translate-x-full'"
>
    <div class="flex h-16 items-center gap-2 border-b border-white/10 px-5">
        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-600 font-bold text-white">H</span>
        <div class="leading-tight">
            <p class="text-sm font-semibold text-white">{{ $isSuper ? 'Platform Admin' : (current_tenant()->name ?? config('app.name')) }}</p>
            <p class="text-xs text-slate-400">{{ $isSuper ? 'Haji Umroh SaaS' : 'Travel Haji & Umroh' }}</p>
        </div>
    </div>

    <nav class="px-3 py-4">
        <ul class="space-y-1">
            @foreach($nav as $item)
                @php $active = request()->routeIs($item['pattern']); @endphp
                <li>
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition
                       {{ $active ? 'bg-brand-600 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <span class="h-1.5 w-1.5 rounded-full {{ $active ? 'bg-white' : 'bg-slate-500' }}"></span>
                        {{ $item['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>
</aside>
