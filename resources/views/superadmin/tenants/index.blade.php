@extends('layouts.app')
@section('title', 'Travel / Tenant')

@section('content')
    <x-page-header title="Daftar Travel" subtitle="Kelola seluruh travel yang terdaftar di platform.">
        <x-slot:actions>
            <a href="{{ route('admin.tenants.create') }}" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">+ Travel Baru</a>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" class="mb-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama travel..."
               class="w-full max-w-sm rounded-lg border-slate-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
    </form>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-5 py-3">Travel</th>
                    <th class="px-5 py-3">Slug</th>
                    <th class="px-5 py-3">Pengguna</th>
                    <th class="px-5 py-3">Paket</th>
                    <th class="px-5 py-3">Jamaah</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($tenants as $tenant)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3 font-medium text-slate-800">{{ $tenant->name }}</td>
                        <td class="px-5 py-3 text-slate-500">{{ $tenant->slug }}</td>
                        <td class="px-5 py-3">{{ $tenant->users_count }}</td>
                        <td class="px-5 py-3">{{ $tenant->packages_count }}</td>
                        <td class="px-5 py-3">{{ $tenant->jamaah_count }}</td>
                        <td class="px-5 py-3"><x-badge :color="$tenant->is_active ? 'emerald' : 'rose'">{{ $tenant->is_active ? 'Aktif' : 'Non-aktif' }}</x-badge></td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.tenants.show', $tenant) }}" class="text-brand-600 hover:underline">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-8 text-center text-slate-400">Belum ada travel.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $tenants->links() }}</div>
@endsection
