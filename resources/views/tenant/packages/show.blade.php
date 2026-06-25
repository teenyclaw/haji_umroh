@extends('layouts.app')
@section('title', $package->name)

@section('content')
    <x-page-header :title="$package->name" :subtitle="$package->type->label()">
        <x-slot:actions>
            <a href="{{ route('app.bookings.create', ['package_id' => $package->id]) }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Buat Booking</a>
            <a href="{{ route('app.packages.edit', $package) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Edit</a>
            <form method="POST" action="{{ route('app.packages.destroy', $package) }}" onsubmit="return confirm('Hapus paket ini?')">
                @csrf @method('DELETE')
                <button class="rounded-lg border border-rose-200 px-4 py-2 text-sm font-medium text-rose-600 hover:bg-rose-50">Hapus</button>
            </form>
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="mb-4 font-semibold text-slate-800">Detail Paket</h3>
                <dl class="grid gap-3 text-sm sm:grid-cols-2">
                    <div><dt class="text-slate-500">Durasi</dt><dd class="font-medium">{{ $package->duration_days ?? '-' }} hari</dd></div>
                    <div><dt class="text-slate-500">Kuota</dt><dd class="font-medium">{{ $package->quota }} jamaah</dd></div>
                    <div><dt class="text-slate-500">Berangkat</dt><dd class="font-medium">{{ tanggal_id($package->departure_date) }}</dd></div>
                    <div><dt class="text-slate-500">Pulang</dt><dd class="font-medium">{{ tanggal_id($package->return_date) }}</dd></div>
                    <div><dt class="text-slate-500">Maskapai</dt><dd class="font-medium">{{ $package->airline ?? '-' }}</dd></div>
                    <div><dt class="text-slate-500">Bandara</dt><dd class="font-medium">{{ $package->departure_airport ?? '-' }}</dd></div>
                    <div><dt class="text-slate-500">Hotel Makkah</dt><dd class="font-medium">{{ $package->hotel_makkah ?? '-' }} ({{ $package->hotel_makkah_star ?? '-' }}&#9733;)</dd></div>
                    <div><dt class="text-slate-500">Hotel Madinah</dt><dd class="font-medium">{{ $package->hotel_madinah ?? '-' }} ({{ $package->hotel_madinah_star ?? '-' }}&#9733;)</dd></div>
                </dl>
                @if($package->description)
                    <p class="mt-4 border-t border-slate-100 pt-4 text-sm text-slate-600">{{ $package->description }}</p>
                @endif
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="mb-4 font-semibold text-slate-800">Booking pada Paket Ini</h3>
                <div class="divide-y divide-slate-100">
                    @forelse($package->bookings as $booking)
                        <div class="flex items-center justify-between py-2 text-sm">
                            <a href="{{ route('app.bookings.show', $booking) }}" class="font-medium text-brand-600 hover:underline">{{ $booking->code }}</a>
                            <span class="text-slate-500">{{ rupiah($booking->total_amount) }}</span>
                            <x-badge :color="$booking->status->color()">{{ $booking->status->label() }}</x-badge>
                        </div>
                    @empty
                        <p class="py-4 text-center text-sm text-slate-400">Belum ada booking.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="mb-4 font-semibold text-slate-800">Harga per Tipe Kamar</h3>
                <div class="space-y-2 text-sm">
                    @forelse($package->prices as $price)
                        <div class="flex items-center justify-between">
                            <span class="text-slate-600">{{ $price->room_type->label() }}</span>
                            <span class="font-semibold text-slate-800">{{ rupiah($price->price) }}</span>
                        </div>
                    @empty
                        <p class="text-slate-400">Harga dasar: {{ rupiah($package->base_price) }}</p>
                    @endforelse
                </div>
                <div class="mt-4 space-y-1 border-t border-slate-100 pt-3 text-xs text-slate-500">
                    <div class="flex justify-between"><span>Handling</span><span>{{ rupiah($package->handling_fee) }}</span></div>
                    <div class="flex justify-between"><span>Asuransi</span><span>{{ rupiah($package->insurance_fee) }}</span></div>
                    <div class="flex justify-between"><span>Visa</span><span>{{ rupiah($package->visa_fee) }}</span></div>
                </div>
            </div>
        </div>
    </div>
@endsection
