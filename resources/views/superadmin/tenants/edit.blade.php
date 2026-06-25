@extends('layouts.app')
@section('title', 'Edit ' . $tenant->name)

@section('content')
    <x-page-header title="Edit Travel" :subtitle="$tenant->name" />

    <form method="POST" action="{{ route('admin.tenants.update', $tenant) }}" class="max-w-3xl space-y-6">
        @csrf @method('PUT')
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="grid gap-4 sm:grid-cols-2">
                <x-input label="Nama Travel" name="name" :value="$tenant->name" required />
                <x-input label="Slug" name="slug" :value="$tenant->slug" required />
                <x-input label="Telepon" name="phone" :value="$tenant->phone" />
                <x-input label="Email" name="email" type="email" :value="$tenant->email" />
                <x-input label="Kota" name="city" :value="$tenant->city" />
                <x-select label="Paket Langganan" name="subscription_plan" :selected="$tenant->subscription_plan"
                          :options="['trial' => 'Trial', 'basic' => 'Basic', 'pro' => 'Pro', 'enterprise' => 'Enterprise']" required />
            </div>
        </div>
        <div class="flex justify-end gap-2">
            <a href="{{ route('admin.tenants.show', $tenant) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</a>
            <button class="rounded-lg bg-brand-600 px-5 py-2 text-sm font-semibold text-white hover:bg-brand-700">Simpan</button>
        </div>
    </form>
@endsection
