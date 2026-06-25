@extends('layouts.app')
@section('title', $tenant->name)

@section('content')
    <x-page-header :title="$tenant->name" :subtitle="$tenant->city">
        <x-slot:actions>
            <a href="{{ route('admin.tenants.edit', $tenant) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Edit</a>
            <form method="POST" action="{{ route('admin.tenants.toggle', $tenant) }}">
                @csrf
                <button class="rounded-lg px-4 py-2 text-sm font-semibold text-white {{ $tenant->is_active ? 'bg-rose-600 hover:bg-rose-700' : 'bg-emerald-600 hover:bg-emerald-700' }}">
                    {{ $tenant->is_active ? 'Non-aktifkan' : 'Aktifkan' }}
                </button>
            </form>
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-4 sm:grid-cols-4">
        @php
            $cards = [
                ['Pengguna', $tenant->users_count], ['Paket', $tenant->packages_count],
                ['Jamaah', $tenant->jamaah_count], ['Booking', $tenant->bookings_count],
            ];
        @endphp
        @foreach($cards as [$label, $value])
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">{{ $label }}</p>
                <p class="mt-2 text-2xl font-bold text-slate-800">{{ $value }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="mb-4 font-semibold text-slate-800">Informasi Travel</h3>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">Slug Portal</dt><dd class="font-medium">/t/{{ $tenant->slug }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Telepon</dt><dd>{{ $tenant->phone ?? '-' }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Email</dt><dd>{{ $tenant->email ?? '-' }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Paket Langganan</dt><dd class="capitalize">{{ $tenant->subscription_plan }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Status</dt><dd><x-badge :color="$tenant->is_active ? 'emerald' : 'rose'">{{ $tenant->is_active ? 'Aktif' : 'Non-aktif' }}</x-badge></dd></div>
            </dl>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="mb-4 font-semibold text-slate-800">Pengguna</h3>
            <div class="space-y-2">
                @foreach($users as $user)
                    <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2 text-sm">
                        <div>
                            <p class="font-medium text-slate-700">{{ $user->name }}</p>
                            <p class="text-xs text-slate-400">{{ $user->email }}</p>
                        </div>
                        <x-badge color="blue">{{ $user->getRoleNames()->first() ?? 'user' }}</x-badge>
                    </div>
                @endforeach
            </div>

            <form method="POST" action="{{ route('admin.tenants.reset-password', $tenant) }}" class="mt-4 flex items-end gap-2">
                @csrf
                <div class="flex-1">
                    <label class="mb-1 block text-xs font-medium text-slate-600">Reset kata sandi owner</label>
                    <input type="password" name="password" required placeholder="Kata sandi baru"
                           class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <button class="rounded-lg bg-slate-700 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Reset</button>
            </form>
        </div>
    </div>
@endsection
