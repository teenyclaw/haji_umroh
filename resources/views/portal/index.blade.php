@extends('layouts.portal')
@section('title', 'Beranda')

@section('content')
    <section class="text-white" style="background: linear-gradient(135deg, var(--brand), #064e3b)">
        <div class="mx-auto max-w-6xl px-4 py-20 text-center">
            <h1 class="text-3xl font-bold sm:text-4xl">Wujudkan Perjalanan Ibadah Anda bersama {{ $tenant->name }}</h1>
            <p class="mx-auto mt-4 max-w-2xl text-white/90">Paket haji dan umroh terpercaya dengan pelayanan terbaik, hotel nyaman, dan bimbingan ibadah yang amanah.</p>
            <a href="{{ route('portal.packages', $tenant->slug) }}" class="mt-8 inline-block rounded-lg bg-white px-6 py-3 text-sm font-semibold" style="color: var(--brand)">Lihat Paket</a>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-16">
        <h2 class="mb-6 text-2xl font-bold text-slate-800">Paket Pilihan</h2>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($packages as $package)
                @include('portal._package_card', ['package' => $package, 'tenant' => $tenant])
            @empty
                <p class="col-span-full text-center text-slate-400">Belum ada paket tersedia saat ini.</p>
            @endforelse
        </div>
    </section>

    <section class="mx-auto max-w-3xl px-4 pb-16">
        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800">Tertarik? Hubungi Kami</h2>
            <p class="mt-1 text-sm text-slate-500">Isi formulir berikut, tim kami akan segera menghubungi Anda.</p>
            <form method="POST" action="{{ route('portal.inquiry', $tenant->slug) }}" class="mt-6 grid gap-4 sm:grid-cols-2">
                @csrf
                <input name="name" placeholder="Nama Lengkap" required class="rounded-lg border-slate-300 text-sm shadow-sm">
                <input name="phone" placeholder="No. WhatsApp" required class="rounded-lg border-slate-300 text-sm shadow-sm">
                <input name="email" type="email" placeholder="Email (opsional)" class="rounded-lg border-slate-300 text-sm shadow-sm sm:col-span-2">
                <textarea name="message" rows="3" placeholder="Pesan / paket yang diminati" class="rounded-lg border-slate-300 text-sm shadow-sm sm:col-span-2"></textarea>
                <button class="rounded-lg py-2.5 text-sm font-semibold text-white sm:col-span-2" style="background: var(--brand)">Kirim Permintaan</button>
            </form>
        </div>
    </section>
@endsection
