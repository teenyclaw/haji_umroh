@extends('layouts.app')
@section('title', 'Rombongan')

@section('content')
    <x-page-header title="Rombongan" subtitle="Kelompok keberangkatan jamaah.">
        <x-slot:actions>
            <a href="{{ route('app.rombongan.create') }}" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">+ Rombongan Baru</a>
        </x-slot:actions>
    </x-page-header>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                <tr><th class="px-5 py-3">Kode</th><th class="px-5 py-3">Nama</th><th class="px-5 py-3">Paket</th><th class="px-5 py-3">Ketua</th><th class="px-5 py-3">Anggota</th><th class="px-5 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($rombongan as $rom)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3 font-medium text-slate-800">{{ $rom->code }}</td>
                        <td class="px-5 py-3">{{ $rom->name }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ $rom->package?->name ?? '-' }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ $rom->leader_name ?? '-' }}</td>
                        <td class="px-5 py-3">{{ $rom->members_count }}</td>
                        <td class="px-5 py-3 text-right"><a href="{{ route('app.rombongan.show', $rom) }}" class="text-brand-600 hover:underline">Kelola</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-slate-400">Belum ada rombongan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $rombongan->links() }}</div>
@endsection
