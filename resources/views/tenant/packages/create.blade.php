@extends('layouts.app')
@section('title', 'Paket Baru')

@section('content')
    <x-page-header title="Buat Paket Baru" />

    <form method="POST" action="{{ route('app.packages.store') }}" enctype="multipart/form-data" class="max-w-4xl space-y-6">
        @csrf
        @include('tenant.packages._form')
        <div class="flex justify-end gap-2">
            <a href="{{ route('app.packages.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</a>
            <button class="rounded-lg bg-brand-600 px-5 py-2 text-sm font-semibold text-white hover:bg-brand-700">Simpan Paket</button>
        </div>
    </form>
@endsection
