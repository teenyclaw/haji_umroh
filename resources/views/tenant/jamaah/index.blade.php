@extends('layouts.app')
@section('title', 'Jamaah')

@section('content')
    <x-page-header title="Data Jamaah" subtitle="Kelola data calon jamaah haji dan umroh.">
        <x-slot:actions>
            <a href="{{ route('app.jamaah.create') }}" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">+ Jamaah Baru</a>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" class="mb-4 flex flex-wrap gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIK, atau HP..."
               class="w-full max-w-xs rounded-lg border-slate-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
        <select name="status" class="rounded-lg border-slate-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
            <option value="">Semua Status</option>
            @foreach($statuses as $key => $label)
                <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <button class="rounded-lg bg-slate-700 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Filter</button>
    </form>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Nama</th>
                        <th class="px-5 py-3">NIK</th>
                        <th class="px-5 py-3">Gender</th>
                        <th class="px-5 py-3">Kontak</th>
                        <th class="px-5 py-3">Agent</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($jamaah as $j)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3 font-medium text-slate-800">{{ $j->full_name }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ $j->nik ?? '-' }}</td>
                            <td class="px-5 py-3">{{ $j->gender?->label() }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ $j->phone ?? '-' }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ $j->agent?->name ?? '-' }}</td>
                            <td class="px-5 py-3"><x-badge :color="$j->status->color()">{{ $j->status->label() }}</x-badge></td>
                            <td class="px-5 py-3 text-right"><a href="{{ route('app.jamaah.show', $j) }}" class="text-brand-600 hover:underline">Detail</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-8 text-center text-slate-400">Belum ada data jamaah.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $jamaah->links() }}</div>
@endsection
