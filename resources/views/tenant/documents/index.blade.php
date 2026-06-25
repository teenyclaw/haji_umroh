@extends('layouts.app')
@section('title', 'Verifikasi Dokumen')

@section('content')
    <x-page-header title="Verifikasi Dokumen" subtitle="Antrian dokumen jamaah yang perlu diverifikasi." />

    <form method="GET" class="mb-4 flex flex-wrap gap-2">
        <select name="status" class="rounded-lg border-slate-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
            <option value="">Menunggu Verifikasi</option>
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
                        <th class="px-5 py-3">Jamaah</th>
                        <th class="px-5 py-3">Dokumen</th>
                        <th class="px-5 py-3">Berkas</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($documents as $doc)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3"><a href="{{ route('app.jamaah.show', $doc->jamaah) }}" class="font-medium text-brand-600 hover:underline">{{ $doc->jamaah->full_name }}</a></td>
                            <td class="px-5 py-3 text-slate-600">{{ $doc->documentType->name }}</td>
                            <td class="px-5 py-3">@if($doc->fileUrl())<a href="{{ $doc->fileUrl() }}" target="_blank" class="text-brand-600 hover:underline">Lihat</a>@else - @endif</td>
                            <td class="px-5 py-3"><x-badge :color="$doc->status->color()">{{ $doc->status->label() }}</x-badge></td>
                            <td class="px-5 py-3 text-right">
                                @if($doc->status->value === 'upload')
                                    <div class="flex justify-end gap-1">
                                        <form method="POST" action="{{ route('app.documents.verify', $doc) }}">@csrf<button class="rounded bg-emerald-600 px-2 py-1 text-xs text-white hover:bg-emerald-700">Verifikasi</button></form>
                                        <form method="POST" action="{{ route('app.documents.reject', $doc) }}" onsubmit="return confirm('Tolak dokumen ini?')">@csrf<button class="rounded bg-rose-600 px-2 py-1 text-xs text-white hover:bg-rose-700">Tolak</button></form>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-8 text-center text-slate-400">Tidak ada dokumen.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $documents->links() }}</div>
@endsection
