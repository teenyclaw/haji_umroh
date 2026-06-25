@extends('layouts.app')
@section('title', 'Travel Baru')

@section('content')
    <x-page-header title="Buat Travel Baru" subtitle="Sistem akan membuat akun owner dan data awal secara otomatis." />

    <form method="POST" action="{{ route('admin.tenants.store') }}" class="max-w-3xl space-y-6">
        @csrf
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="mb-4 font-semibold text-slate-800">Data Travel</h3>
            <div class="grid gap-4 sm:grid-cols-2">
                <x-input label="Nama Travel" name="name" required />
                <x-input label="Slug (URL portal)" name="slug" required placeholder="contoh: barokah-tour" />
                <x-input label="Telepon / WhatsApp" name="phone" />
                <x-input label="Email" name="email" type="email" />
                <x-input label="Kota" name="city" />
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="mb-4 font-semibold text-slate-800">Akun Owner</h3>
            <div class="grid gap-4 sm:grid-cols-2">
                <x-input label="Nama Owner" name="owner_name" required />
                <x-input label="Email Owner" name="owner_email" type="email" required />
                <x-input label="Kata Sandi" name="owner_password" type="password" required />
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('admin.tenants.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</a>
            <button type="submit" class="rounded-lg bg-brand-600 px-5 py-2 text-sm font-semibold text-white hover:bg-brand-700">Simpan Travel</button>
        </div>
    </form>
@endsection
