@extends('layouts.app')
@section('title', 'Dashboard Platform')

@section('content')
    <x-page-header title="Dashboard Platform" subtitle="Ringkasan seluruh travel di platform." />

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        @php
            $cards = [
                ['Total Travel', $stats['tenants'], 'teal'],
                ['Travel Aktif', $stats['active_tenants'], 'emerald'],
                ['Total Pengguna', $stats['users'], 'blue'],
                ['Total Jamaah', $stats['jamaah'], 'indigo'],
                ['Total Booking', $stats['bookings'], 'violet'],
            ];
        @endphp
        @foreach($cards as [$label, $value, $color])
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">{{ $label }}</p>
                <p class="mt-2 text-3xl font-bold text-{{ $color }}-600">{{ number_format($value, 0, ',', '.') }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <h3 class="font-semibold text-slate-800">Travel Terbaru</h3>
            <a href="{{ route('admin.tenants.index') }}" class="text-sm font-medium text-brand-600 hover:underline">Lihat semua</a>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($tenants as $tenant)
                <div class="flex items-center justify-between px-5 py-3">
                    <div>
                        <a href="{{ route('admin.tenants.show', $tenant) }}" class="font-medium text-slate-800 hover:text-brand-600">{{ $tenant->name }}</a>
                        <p class="text-xs text-slate-400">{{ $tenant->city ?? '-' }} &middot; {{ $tenant->users_count }} pengguna</p>
                    </div>
                    <x-badge :color="$tenant->is_active ? 'emerald' : 'rose'">{{ $tenant->is_active ? 'Aktif' : 'Non-aktif' }}</x-badge>
                </div>
            @empty
                <p class="px-5 py-8 text-center text-sm text-slate-400">Belum ada travel. <a href="{{ route('admin.tenants.create') }}" class="text-brand-600">Buat travel pertama</a>.</p>
            @endforelse
        </div>
    </div>
@endsection
