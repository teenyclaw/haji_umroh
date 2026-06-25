@extends('layouts.portal')
@section('title', $package->name)

@section('content')
    <section class="mx-auto max-w-5xl px-4 py-12">
        <a href="{{ route('portal.packages', $tenant->slug) }}" class="text-sm text-slate-500 hover:text-slate-800">&larr; Kembali ke daftar paket</a>

        <div class="mt-4 grid gap-8 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="overflow-hidden rounded-2xl">
                    <div class="flex h-56 items-center justify-center text-white" style="background: linear-gradient(135deg, var(--brand), #064e3b)">
                        @if($package->imageUrl())
                            <img src="{{ $package->imageUrl() }}" class="h-full w-full object-cover" alt="">
                        @else
                            <span class="text-2xl font-bold">{{ $package->type->label() }}</span>
                        @endif
                    </div>
                </div>
                <h1 class="mt-6 text-2xl font-bold text-slate-800">{{ $package->name }}</h1>
                <p class="mt-2 text-slate-600">{{ $package->description }}</p>

                <div class="mt-6 grid gap-4 rounded-2xl border border-slate-200 bg-white p-6 sm:grid-cols-2">
                    <div><p class="text-xs text-slate-500">Durasi</p><p class="font-medium">{{ $package->duration_days ?? '-' }} hari</p></div>
                    <div><p class="text-xs text-slate-500">Keberangkatan</p><p class="font-medium">{{ tanggal_id($package->departure_date) }}</p></div>
                    <div><p class="text-xs text-slate-500">Maskapai</p><p class="font-medium">{{ $package->airline ?? '-' }}</p></div>
                    <div><p class="text-xs text-slate-500">Bandara</p><p class="font-medium">{{ $package->departure_airport ?? '-' }}</p></div>
                    <div><p class="text-xs text-slate-500">Hotel Makkah</p><p class="font-medium">{{ $package->hotel_makkah ?? '-' }} ({{ $package->hotel_makkah_star ?? '-' }}&#9733;)</p></div>
                    <div><p class="text-xs text-slate-500">Hotel Madinah</p><p class="font-medium">{{ $package->hotel_madinah ?? '-' }} ({{ $package->hotel_madinah_star ?? '-' }}&#9733;)</p></div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-2xl border border-slate-200 bg-white p-6">
                    <h3 class="mb-3 font-semibold text-slate-800">Harga per Tipe Kamar</h3>
                    <div class="space-y-2 text-sm">
                        @forelse($package->prices as $price)
                            <div class="flex items-center justify-between"><span class="text-slate-600">{{ $price->room_type->label() }}</span><span class="font-semibold">{{ rupiah($price->price) }}</span></div>
                        @empty
                            <p class="font-semibold">{{ rupiah($package->base_price) }}</p>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6">
                    <h3 class="mb-3 font-semibold text-slate-800">Daftar Minat</h3>
                    <form method="POST" action="{{ route('portal.inquiry', $tenant->slug) }}" class="space-y-3">
                        @csrf
                        <input type="hidden" name="package_id" value="{{ $package->id }}">
                        <input name="name" placeholder="Nama Lengkap" required class="w-full rounded-lg border-slate-300 text-sm">
                        <input name="phone" placeholder="No. WhatsApp" required class="w-full rounded-lg border-slate-300 text-sm">
                        <textarea name="message" rows="2" placeholder="Pesan (opsional)" class="w-full rounded-lg border-slate-300 text-sm"></textarea>
                        <button class="w-full rounded-lg py-2.5 text-sm font-semibold text-white" style="background: var(--brand)">Kirim</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
