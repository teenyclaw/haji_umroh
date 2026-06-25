@extends('layouts.app')
@section('title', $jamaah->full_name)

@section('content')
    <x-page-header :title="$jamaah->full_name" :subtitle="$jamaah->nik">
        <x-slot:actions>
            <a href="{{ route('app.jamaah.edit', $jamaah) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Edit</a>
            <form method="POST" action="{{ route('app.jamaah.destroy', $jamaah) }}" onsubmit="return confirm('Hapus jamaah ini?')">
                @csrf @method('DELETE')
                <button class="rounded-lg border border-rose-200 px-4 py-2 text-sm font-medium text-rose-600 hover:bg-rose-50">Hapus</button>
            </form>
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm">
                @if($jamaah->photoUrl())
                    <img src="{{ $jamaah->photoUrl() }}" class="mx-auto h-24 w-24 rounded-full object-cover" alt="">
                @else
                    <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-brand-100 text-2xl font-bold text-brand-700">{{ strtoupper(substr($jamaah->full_name, 0, 1)) }}</div>
                @endif
                <h3 class="mt-3 font-semibold text-slate-800">{{ $jamaah->full_name }}</h3>
                <div class="mt-1"><x-badge :color="$jamaah->status->color()">{{ $jamaah->status->label() }}</x-badge></div>
                @if($jamaah->passportExpiringSoon())
                    <p class="mt-3 rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-700">Paspor mendekati kedaluwarsa: {{ tanggal_id($jamaah->passport_expired_at) }}</p>
                @endif
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="mb-3 font-semibold text-slate-800">Kelengkapan Dokumen</h3>
                <div class="mb-2 flex items-center justify-between text-sm">
                    <span class="text-slate-500">{{ $completion['verified'] }}/{{ $completion['total'] }} terverifikasi</span>
                    <span class="font-semibold text-brand-600">{{ $completion['percentage'] }}%</span>
                </div>
                <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                    <div class="h-full bg-brand-600" style="width: {{ $completion['percentage'] }}%"></div>
                </div>
                <form method="POST" action="{{ route('app.documents.sync', $jamaah) }}" class="mt-3">
                    @csrf
                    <button class="text-xs font-medium text-brand-600 hover:underline">Perbarui checklist dokumen</button>
                </form>
            </div>
        </div>

        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="mb-4 font-semibold text-slate-800">Informasi Pribadi</h3>
                <dl class="grid gap-3 text-sm sm:grid-cols-2">
                    <div><dt class="text-slate-500">TTL</dt><dd class="font-medium">{{ $jamaah->birth_place }}, {{ tanggal_id($jamaah->birth_date) }}</dd></div>
                    <div><dt class="text-slate-500">Jenis Kelamin</dt><dd class="font-medium">{{ $jamaah->gender?->label() }}</dd></div>
                    <div><dt class="text-slate-500">No. HP</dt><dd class="font-medium">{{ $jamaah->phone ?? '-' }}</dd></div>
                    <div><dt class="text-slate-500">WhatsApp</dt><dd class="font-medium">{{ $jamaah->whatsapp ?? '-' }}</dd></div>
                    <div><dt class="text-slate-500">Paspor</dt><dd class="font-medium">{{ $jamaah->passport_number ?? '-' }}</dd></div>
                    <div><dt class="text-slate-500">Mahram</dt><dd class="font-medium">{{ $jamaah->mahram_name ? $jamaah->mahram_name . ' (' . $jamaah->mahram_relation . ')' : '-' }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-slate-500">Alamat</dt><dd class="font-medium">{{ $jamaah->address ?? '-' }}</dd></div>
                </dl>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="mb-4 font-semibold text-slate-800">Checklist Dokumen</h3>
                <div class="space-y-2">
                    @forelse($jamaah->documents as $doc)
                        <div class="flex flex-wrap items-center justify-between gap-2 rounded-lg border border-slate-100 bg-slate-50 px-3 py-2">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-slate-700">{{ $doc->documentType->name }}</span>
                                <x-badge :color="$doc->status->color()">{{ $doc->status->label() }}</x-badge>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($doc->fileUrl())
                                    <a href="{{ $doc->fileUrl() }}" target="_blank" class="text-xs text-brand-600 hover:underline">Lihat</a>
                                @endif
                                <form method="POST" action="{{ route('app.documents.upload', $doc) }}" enctype="multipart/form-data" class="flex items-center gap-1">
                                    @csrf
                                    <input type="file" name="file" required class="w-36 text-xs file:mr-1 file:rounded file:border-0 file:bg-white file:px-2 file:py-1 file:text-xs">
                                    <button class="rounded bg-slate-700 px-2 py-1 text-xs text-white hover:bg-slate-800">Upload</button>
                                </form>
                                @if($doc->status->value === 'upload')
                                    <form method="POST" action="{{ route('app.documents.verify', $doc) }}">
                                        @csrf
                                        <button class="rounded bg-emerald-600 px-2 py-1 text-xs text-white hover:bg-emerald-700">Verifikasi</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">Belum ada checklist. Klik "Perbarui checklist dokumen".</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="mb-4 font-semibold text-slate-800">Riwayat Booking</h3>
                <div class="divide-y divide-slate-100">
                    @forelse($jamaah->bookingJamaah as $bj)
                        <div class="flex items-center justify-between py-2 text-sm">
                            <div>
                                <a href="{{ route('app.bookings.show', $bj->booking) }}" class="font-medium text-brand-600 hover:underline">{{ $bj->booking->code }}</a>
                                <span class="text-slate-400"> &middot; {{ $bj->booking->package->name }}</span>
                            </div>
                            <span class="text-slate-600">{{ $bj->room_type->label() }} &middot; {{ rupiah($bj->price) }}</span>
                        </div>
                    @empty
                        <p class="py-2 text-sm text-slate-400">Belum ada booking.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
