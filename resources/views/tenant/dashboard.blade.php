@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <x-page-header :title="'Assalamu\'alaikum, ' . auth()->user()->name" subtitle="Ringkasan operasional travel Anda hari ini." />

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
        @php
            $cards = [
                ['Jamaah Aktif', number_format($stats['jamaah'], 0, ',', '.'), 'teal'],
                ['Booking Bln Ini', $stats['bookings_month'], 'blue'],
                ['Paket Aktif', $stats['packages'], 'indigo'],
                ['Bayar Pending', $stats['pending_payments'], 'amber'],
                ['Dok. Pending', $stats['pending_documents'], 'orange'],
            ];
        @endphp
        @foreach($cards as [$label, $value, $color])
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs text-slate-500">{{ $label }}</p>
                <p class="mt-2 text-2xl font-bold text-{{ $color }}-600">{{ $value }}</p>
            </div>
        @endforeach
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs text-slate-500">Total Piutang</p>
            <p class="mt-2 text-xl font-bold text-rose-600">{{ rupiah($stats['piutang']) }}</p>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-2">
            <div class="border-b border-slate-100 px-5 py-4"><h3 class="font-semibold text-slate-800">Pembayaran Menunggu Verifikasi</h3></div>
            <div class="divide-y divide-slate-100">
                @forelse($pendingPayments as $payment)
                    <div class="flex items-center justify-between px-5 py-3">
                        <div>
                            <p class="text-sm font-medium text-slate-700">{{ $payment->invoice?->booking?->package?->name ?? 'Paket' }}</p>
                            <p class="text-xs text-slate-400">{{ $payment->number }} &middot; {{ tanggal_id($payment->payment_date) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-slate-800">{{ rupiah($payment->amount) }}</p>
                            <a href="{{ route('app.payments.index') }}" class="text-xs text-brand-600 hover:underline">Verifikasi</a>
                        </div>
                    </div>
                @empty
                    <p class="px-5 py-8 text-center text-sm text-slate-400">Tidak ada pembayaran menunggu verifikasi.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4"><h3 class="font-semibold text-slate-800">Keberangkatan Terdekat</h3></div>
            <div class="divide-y divide-slate-100">
                @forelse($upcomingDepartures as $package)
                    <div class="px-5 py-3">
                        <p class="text-sm font-medium text-slate-700">{{ $package->name }}</p>
                        <p class="text-xs text-slate-400">{{ tanggal_id($package->departure_date, true) }}</p>
                    </div>
                @empty
                    <p class="px-5 py-8 text-center text-sm text-slate-400">Belum ada jadwal keberangkatan.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4"><h3 class="font-semibold text-slate-800">Paspor Mendekati Kedaluwarsa</h3></div>
            <div class="divide-y divide-slate-100">
                @forelse($expiringPassports as $jamaah)
                    <div class="flex items-center justify-between px-5 py-3">
                        <div>
                            <p class="text-sm font-medium text-slate-700">{{ $jamaah->full_name }}</p>
                            <p class="text-xs text-slate-400">Paspor: {{ $jamaah->passport_number ?? '-' }}</p>
                        </div>
                        <x-badge color="amber">{{ tanggal_id($jamaah->passport_expired_at) }}</x-badge>
                    </div>
                @empty
                    <p class="px-5 py-8 text-center text-sm text-slate-400">Tidak ada paspor yang akan kedaluwarsa.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="mb-4 font-semibold text-slate-800">Distribusi Status Jamaah</h3>
            <div class="space-y-2">
                @forelse($statusDistribution as $status => $total)
                    @php $enum = \App\Enums\JamaahStatus::tryFrom($status); @endphp
                    <div class="flex items-center justify-between text-sm">
                        <x-badge :color="$enum?->color() ?? 'slate'">{{ $enum?->label() ?? $status }}</x-badge>
                        <span class="font-medium text-slate-700">{{ $total }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Belum ada data jamaah.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
