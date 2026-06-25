@extends('layouts.portal')
@section('title', 'Paket')

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-12">
        <h1 class="mb-6 text-2xl font-bold text-slate-800">Paket Haji &amp; Umroh</h1>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($packages as $package)
                @include('portal._package_card', ['package' => $package, 'tenant' => $tenant])
            @empty
                <p class="col-span-full text-center text-slate-400">Belum ada paket tersedia.</p>
            @endforelse
        </div>
        <div class="mt-8">{{ $packages->withQueryString()->links() }}</div>
    </section>
@endsection
