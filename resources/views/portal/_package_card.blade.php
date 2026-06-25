<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:shadow-md">
    <div class="flex h-40 items-center justify-center text-white" style="background: linear-gradient(135deg, var(--brand), #047857)">
        @if($package->imageUrl())
            <img src="{{ $package->imageUrl() }}" class="h-full w-full object-cover" alt="">
        @else
            <span class="text-lg font-semibold">{{ $package->type->label() }}</span>
        @endif
    </div>
    <div class="p-5">
        <span class="inline-block rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">{{ $package->type->label() }}</span>
        <h3 class="mt-2 font-semibold text-slate-800">{{ $package->name }}</h3>
        <p class="mt-1 text-xs text-slate-500">{{ $package->duration_days ?? '-' }} hari &middot; {{ tanggal_id($package->departure_date) }}</p>
        <p class="mt-3 text-xl font-bold" style="color: var(--brand)">Mulai {{ rupiah($package->lowestPrice()) }}</p>
        <a href="{{ route('portal.package', [$tenant->slug, $package]) }}" class="mt-4 block rounded-lg border border-slate-200 py-2 text-center text-sm font-medium text-slate-700 hover:bg-slate-50">Lihat Detail</a>
    </div>
</div>
