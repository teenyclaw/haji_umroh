@extends('layouts.app')
@section('title', 'Pengaturan')

@section('content')
    <x-page-header title="Pengaturan Travel" subtitle="Profil, rekening, dan preferensi sistem." />

    <form method="POST" action="{{ route('app.settings.update') }}" enctype="multipart/form-data" class="max-w-4xl space-y-6">
        @csrf @method('PUT')

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="mb-4 font-semibold text-slate-800">Profil Travel</h3>
            <div class="grid gap-4 sm:grid-cols-2">
                <x-input label="Nama Travel" name="name" :value="$tenant->name" required />
                <x-input label="No. Izin Kemenag (PPIU/PIHK)" name="izin_number" :value="$tenant->izin_number" />
                <x-input label="Telepon" name="phone" :value="$tenant->phone" />
                <x-input label="WhatsApp" name="whatsapp" :value="$tenant->whatsapp" />
                <x-input label="Email" name="email" type="email" :value="$tenant->email" />
                <x-input label="NPWP" name="npwp" :value="$tenant->npwp" />
                <x-input label="Kota" name="city" :value="$tenant->city" />
                <x-input label="Provinsi" name="province" :value="$tenant->province" />
                <div class="sm:col-span-2"><x-textarea label="Alamat" name="address" :value="$tenant->address" rows="2" /></div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Logo</label>
                    @if($tenant->logoUrl())<img src="{{ $tenant->logoUrl() }}" class="mb-2 h-12">@endif
                    <input type="file" name="logo" accept="image/*" class="w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-sm file:text-brand-700">
                </div>
                <x-input label="Warna Brand" name="brand_color" type="color" :value="$tenant->brand_color" />
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="mb-4 font-semibold text-slate-800">Rekening Bank</h3>
            <div class="grid gap-4 sm:grid-cols-3">
                <x-input label="Nama Bank" name="bank_name" :value="$tenant->bank_name" />
                <x-input label="No. Rekening" name="bank_account_number" :value="$tenant->bank_account_number" />
                <x-input label="Atas Nama" name="bank_account_holder" :value="$tenant->bank_account_holder" />
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="mb-4 font-semibold text-slate-800">Preferensi Sistem</h3>
            <div class="grid gap-4 sm:grid-cols-2">
                <x-input label="Persentase DP Default (%)" name="dp_percentage" type="number" :value="$tenant->dp_percentage" required />
                <label class="flex items-center gap-2 self-end text-sm text-slate-700">
                    <input type="hidden" name="mahram_strict" value="0">
                    <input type="checkbox" name="mahram_strict" value="1" @checked($tenant->mahram_strict) class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                    Wajibkan data mahram untuk jamaah perempuan saat booking
                </label>
            </div>
        </div>

        <div class="flex justify-end">
            <button class="rounded-lg bg-brand-600 px-5 py-2 text-sm font-semibold text-white hover:bg-brand-700">Simpan Pengaturan</button>
        </div>
    </form>
@endsection
