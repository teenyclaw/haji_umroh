@extends('layouts.app')
@section('title', 'Laporan')

@php
    $months = ['', 'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    $maxIncome = $monthlyIncome->max() ?: 1;
@endphp

@section('content')
    <x-page-header title="Laporan & Analitik" subtitle="Ringkasan keuangan dan data jamaah.">
        <x-slot:actions>
            <a href="{{ route('app.reports.jamaah') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Export Jamaah (Excel)</a>
            <a href="{{ route('app.reports.payments') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Export Pembayaran (Excel)</a>
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Total Kas Masuk</p><p class="mt-2 text-2xl font-bold text-emerald-600">{{ rupiah($finance['total_masuk']) }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Kas Masuk Bulan Ini</p><p class="mt-2 text-2xl font-bold text-blue-600">{{ rupiah($finance['masuk_bulan_ini']) }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Total Piutang</p><p class="mt-2 text-2xl font-bold text-rose-600">{{ rupiah($finance['piutang']) }}</p></div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="mb-4 font-semibold text-slate-800">Kas Masuk per Bulan ({{ now()->year }})</h3>
            <div class="flex h-48 items-end gap-2">
                @for($m = 1; $m <= 12; $m++)
                    @php $val = $monthlyIncome[$m] ?? 0; $h = (int) ($val / $maxIncome * 100); @endphp
                    <div class="flex flex-1 flex-col items-center gap-1">
                        <div class="flex w-full items-end" style="height: 150px;">
                            <div class="w-full rounded-t bg-brand-500" style="height: {{ max($h, 1) }}%" title="{{ rupiah($val) }}"></div>
                        </div>
                        <span class="text-[10px] text-slate-400">{{ $months[$m] }}</span>
                    </div>
                @endfor
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="mb-4 font-semibold text-slate-800">Jamaah per Status</h3>
            <div class="space-y-2">
                @forelse($jamaahByStatus as $status => $total)
                    @php $enum = \App\Enums\JamaahStatus::tryFrom($status); @endphp
                    <div class="flex items-center justify-between text-sm">
                        <x-badge :color="$enum?->color() ?? 'slate'">{{ $enum?->label() ?? $status }}</x-badge>
                        <span class="font-medium text-slate-700">{{ $total }} jamaah</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Belum ada data.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-4"><h3 class="font-semibold text-slate-800">Invoice Belum Lunas</h3></div>
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                <tr><th class="px-5 py-3">Nomor</th><th class="px-5 py-3">Paket</th><th class="px-5 py-3">Total</th><th class="px-5 py-3">Sisa</th><th class="px-5 py-3">Status</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($unpaidInvoices as $invoice)
                    <tr>
                        <td class="px-5 py-3"><a href="{{ route('app.invoices.show', $invoice) }}" class="font-medium text-brand-600 hover:underline">{{ $invoice->number }}</a></td>
                        <td class="px-5 py-3 text-slate-600">{{ $invoice->booking?->package?->name }}</td>
                        <td class="px-5 py-3">{{ rupiah($invoice->total_amount) }}</td>
                        <td class="px-5 py-3 text-rose-600">{{ rupiah($invoice->outstanding()) }}</td>
                        <td class="px-5 py-3"><x-badge :color="$invoice->status->color()">{{ $invoice->status->label() }}</x-badge></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-slate-400">Semua invoice sudah lunas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
