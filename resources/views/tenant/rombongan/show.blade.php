@extends('layouts.app')
@section('title', $rombongan->name)

@section('content')
    <x-page-header :title="$rombongan->name" :subtitle="$rombongan->code . ($rombongan->package ? ' · ' . $rombongan->package->name : '')">
        <x-slot:actions>
            <a href="{{ route('app.rombongan.edit', $rombongan) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Edit</a>
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="mb-4 font-semibold text-slate-800">Anggota Rombongan ({{ $rombongan->members->count() }})</h3>
            <div class="space-y-2">
                @forelse($rombongan->members as $member)
                    <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2 text-sm">
                        <div>
                            <p class="font-medium text-slate-700">{{ $member->jamaah->full_name }}</p>
                            <p class="text-xs text-slate-400">{{ $member->room_type->label() }} @if($member->seat_number) &middot; Kursi {{ $member->seat_number }} @endif</p>
                        </div>
                        <form method="POST" action="{{ route('app.rombongan.unassign', $member) }}">
                            @csrf
                            <button class="text-xs text-rose-600 hover:underline">Keluarkan</button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Belum ada anggota.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="mb-4 font-semibold text-slate-800">Tambah Anggota</h3>
            <p class="mb-3 text-xs text-slate-500">Jamaah yang sudah booking @if($rombongan->package) pada paket ini @endif dan belum masuk rombongan.</p>
            <form method="POST" action="{{ route('app.rombongan.assign', $rombongan) }}">
                @csrf
                <div class="max-h-72 space-y-2 overflow-y-auto">
                    @forelse($available as $bj)
                        <label class="flex items-center gap-2 rounded-lg border border-slate-100 px-3 py-2 text-sm hover:bg-slate-50">
                            <input type="checkbox" name="booking_jamaah_ids[]" value="{{ $bj->id }}" class="rounded border-slate-300 text-brand-600">
                            <span>{{ $bj->jamaah->full_name }} <span class="text-xs text-slate-400">({{ $bj->booking->package->name }})</span></span>
                        </label>
                    @empty
                        <p class="text-sm text-slate-400">Tidak ada jamaah tersedia.</p>
                    @endforelse
                </div>
                @if($available->isNotEmpty())
                    <button class="mt-4 w-full rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">Tambahkan ke Rombongan</button>
                @endif
            </form>
        </div>
    </div>
@endsection
