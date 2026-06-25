@extends('layouts.app')
@section('title', 'Paket')

@section('content')
    <x-page-header title="Paket Haji & Umroh" subtitle="Kelola paket perjalanan beserta harga per tipe kamar.">
        <x-slot:actions>
            <a href="{{ route('app.packages.create') }}" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">+ Paket Baru</a>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" class="mb-4 flex flex-wrap gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari paket..."
               class="w-full max-w-xs rounded-lg border-slate-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
        <select name="type" class="rounded-lg border-slate-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
            <option value="">Semua Tipe</option>
            @foreach($types as $key => $label)
                <option value="{{ $key }}" @selected(request('type') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <button class="rounded-lg bg-slate-700 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Filter</button>
    </form>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($packages as $package)
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex h-32 items-center justify-center bg-gradient-to-br from-teal-600 to-emerald-700 text-white">
                    @if($package->imageUrl())
                        <img src="{{ $package->imageUrl() }}" class="h-full w-full object-cover" alt="">
                    @else
                        <span class="text-lg font-semibold">{{ $package->type->label() }}</span>
                    @endif
                </div>
                <div class="p-4">
                    <div class="flex items-start justify-between gap-2">
                        <h3 class="font-semibold text-slate-800">{{ $package->name }}</h3>
                        <x-badge :color="$package->is_active ? 'emerald' : 'slate'">{{ $package->is_active ? 'Aktif' : 'Off' }}</x-badge>
                    </div>
                    <p class="mt-1 text-xs text-slate-400">{{ $package->type->label() }} &middot; {{ $package->duration_days ?? '-' }} hari</p>
                    <p class="mt-2 text-lg font-bold text-brand-600">Mulai {{ rupiah($package->lowestPrice()) }}</p>
                    <p class="mt-1 text-xs text-slate-500">Berangkat: {{ tanggal_id($package->departure_date) }}</p>
                    <div class="mt-3 flex items-center justify-between text-xs text-slate-400">
                        <span>{{ $package->bookings_count }} booking &middot; kuota {{ $package->quota }}</span>
                        <a href="{{ route('app.packages.show', $package) }}" class="font-medium text-brand-600 hover:underline">Detail</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white py-12 text-center text-slate-400">
                Belum ada paket. <a href="{{ route('app.packages.create') }}" class="text-brand-600">Buat paket pertama</a>.
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $packages->links() }}</div>
@endsection
