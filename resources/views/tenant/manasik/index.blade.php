@extends('layouts.app')
@section('title', 'Manasik')

@section('content')
    <x-page-header title="Jadwal Manasik" subtitle="Bimbingan manasik dan absensi jamaah.">
        <x-slot:actions>
            <a href="{{ route('app.manasik.create') }}" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">+ Jadwal Baru</a>
        </x-slot:actions>
    </x-page-header>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                <tr><th class="px-5 py-3">Judul</th><th class="px-5 py-3">Waktu</th><th class="px-5 py-3">Lokasi</th><th class="px-5 py-3">Hadir</th><th class="px-5 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($schedules as $schedule)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3 font-medium text-slate-800">{{ $schedule->title }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ tanggal_id($schedule->scheduled_at, true) }}, {{ $schedule->scheduled_at->format('H:i') }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ $schedule->location ?? '-' }}</td>
                        <td class="px-5 py-3">{{ $schedule->present_count }} jamaah</td>
                        <td class="px-5 py-3 text-right"><a href="{{ route('app.manasik.show', $schedule) }}" class="text-brand-600 hover:underline">Absensi</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-slate-400">Belum ada jadwal manasik.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $schedules->links() }}</div>
@endsection
