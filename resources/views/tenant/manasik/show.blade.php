@extends('layouts.app')
@section('title', $manasik->title)

@section('content')
    <x-page-header :title="$manasik->title" :subtitle="tanggal_id($manasik->scheduled_at, true) . ', ' . $manasik->scheduled_at->format('H:i')">
        <x-slot:actions>
            <a href="{{ route('app.manasik.edit', $manasik) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Edit</a>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs text-slate-500">Lokasi</p><p class="mt-1 font-medium text-slate-800">{{ $manasik->location ?? '-' }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs text-slate-500">Instruktur</p><p class="mt-1 font-medium text-slate-800">{{ $manasik->instructor ?? '-' }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs text-slate-500">Paket</p><p class="mt-1 font-medium text-slate-800">{{ $manasik->package?->name ?? 'Umum' }}</p></div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h3 class="mb-4 font-semibold text-slate-800">Absensi Jamaah</h3>
        <form method="POST" action="{{ route('app.manasik.attendance', $manasik) }}">
            @csrf
            <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($jamaah as $j)
                    <label class="flex items-center gap-2 rounded-lg border border-slate-100 px-3 py-2 text-sm hover:bg-slate-50">
                        <input type="checkbox" name="present[]" value="{{ $j->id }}" @checked($attendanceMap[$j->id]->present ?? false) class="rounded border-slate-300 text-brand-600">
                        {{ $j->full_name }}
                    </label>
                @empty
                    <p class="text-sm text-slate-400">Belum ada jamaah.</p>
                @endforelse
            </div>
            @if($jamaah->isNotEmpty())
                <button class="mt-4 rounded-lg bg-brand-600 px-5 py-2 text-sm font-semibold text-white hover:bg-brand-700">Simpan Absensi</button>
            @endif
        </form>
    </div>
@endsection
