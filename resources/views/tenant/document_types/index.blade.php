@extends('layouts.app')
@section('title', 'Jenis Dokumen')

@section('content')
    <x-page-header title="Jenis Dokumen" subtitle="Atur jenis dokumen yang diperlukan untuk paket haji dan umroh." />

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                        <tr><th class="px-5 py-3">Nama</th><th class="px-5 py-3">Umroh</th><th class="px-5 py-3">Haji</th><th class="px-5 py-3">Aktif</th><th class="px-5 py-3"></th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($types as $type)
                            <tr>
                                <form method="POST" action="{{ route('app.document-types.update', $type) }}">
                                    @csrf @method('PUT')
                                    <td class="px-5 py-3"><input name="name" value="{{ $type->name }}" class="w-full rounded border-slate-200 text-sm"></td>
                                    <td class="px-5 py-3 text-center"><input type="checkbox" name="required_umroh" value="1" @checked($type->required_umroh) class="rounded border-slate-300 text-brand-600"></td>
                                    <td class="px-5 py-3 text-center"><input type="checkbox" name="required_haji" value="1" @checked($type->required_haji) class="rounded border-slate-300 text-brand-600"></td>
                                    <td class="px-5 py-3 text-center"><input type="checkbox" name="is_active" value="1" @checked($type->is_active) class="rounded border-slate-300 text-brand-600"></td>
                                    <td class="px-5 py-3 text-right"><button class="rounded bg-slate-700 px-2 py-1 text-xs text-white hover:bg-slate-800">Simpan</button></td>
                                </form>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-8 text-center text-slate-400">Belum ada jenis dokumen.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="mb-4 font-semibold text-slate-800">Tambah Jenis Dokumen</h3>
            <form method="POST" action="{{ route('app.document-types.store') }}" class="space-y-4">
                @csrf
                <x-input label="Nama Dokumen" name="name" required />
                <x-input label="Kode (opsional)" name="code" />
                <label class="flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="required_umroh" value="1" checked class="rounded border-slate-300 text-brand-600"> Wajib untuk Umroh
                </label>
                <label class="flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="required_haji" value="1" checked class="rounded border-slate-300 text-brand-600"> Wajib untuk Haji
                </label>
                <button class="w-full rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">Tambah</button>
            </form>
        </div>
    </div>
@endsection
