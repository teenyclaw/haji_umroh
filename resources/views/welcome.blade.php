<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} &middot; Platform Travel Haji &amp; Umroh</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full bg-slate-50 text-slate-800">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4">
            <div class="flex items-center gap-2">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-teal-600 font-bold text-white">H</span>
                <span class="font-semibold">{{ config('app.name') }}</span>
            </div>
            <a href="{{ route('login') }}" class="rounded-lg bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700">Masuk</a>
        </div>
    </header>

    <section class="mx-auto max-w-6xl px-4 py-20 text-center">
        <span class="inline-block rounded-full bg-teal-100 px-3 py-1 text-xs font-medium text-teal-700">Multi-Tenant SaaS untuk Travel Indonesia</span>
        <h1 class="mx-auto mt-5 max-w-3xl text-4xl font-bold leading-tight text-slate-900 sm:text-5xl">
            Kelola Travel Haji &amp; Umroh Anda dalam Satu Sistem Terpadu
        </h1>
        <p class="mx-auto mt-5 max-w-2xl text-lg text-slate-600">
            Manajemen jamaah, paket, booking, keuangan, dokumen, manasik, dan keberangkatan
            yang dirancang sesuai kebutuhan travel haji dan umroh di Indonesia.
        </p>
        <div class="mt-8 flex items-center justify-center gap-3">
            <a href="{{ route('login') }}" class="rounded-lg bg-teal-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-teal-700">Masuk ke Dashboard</a>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 pb-24">
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @php
                $features = [
                    ['Manajemen Jamaah', 'Data lengkap jamaah, paspor, mahram, hingga status keberangkatan.'],
                    ['Paket Haji & Umroh', 'Atur paket, kuota, hotel, maskapai, dan harga per tipe kamar.'],
                    ['Booking & Keuangan', 'Booking multi-jamaah, invoice, DP, cicilan, dan verifikasi pembayaran.'],
                    ['Manajemen Dokumen', 'Checklist dokumen per paket dengan upload dan verifikasi berkas.'],
                    ['Rombongan & Manasik', 'Kelompok keberangkatan, alokasi kursi, jadwal dan absensi manasik.'],
                    ['Agent & Komisi', 'Referral agent, perhitungan komisi otomatis, dan laporan.'],
                ];
            @endphp
            @foreach($features as [$title, $desc])
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-teal-100 text-teal-700">&#10003;</div>
                    <h3 class="font-semibold text-slate-800">{{ $title }}</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <footer class="border-t border-slate-200 bg-white py-8 text-center text-sm text-slate-500">
        &copy; {{ date('Y') }} {{ config('app.name') }}. Sistem Informasi Travel Haji &amp; Umroh.
    </footer>
</body>
</html>
