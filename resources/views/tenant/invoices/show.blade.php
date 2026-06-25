@extends('layouts.app')
@section('title', $invoice->number)

@section('content')
    <x-page-header :title="'Invoice ' . $invoice->number" :subtitle="$invoice->booking?->package?->name">
        <x-slot:actions>
            <a href="{{ route('app.invoices.pdf', $invoice) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Unduh PDF</a>
            <a href="{{ route('app.payments.create', ['invoice_id' => $invoice->id]) }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Catat Pembayaran</a>
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="mb-4 font-semibold text-slate-800">Rincian Peserta</h3>
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="text-left text-xs uppercase text-slate-500">
                        <tr><th class="py-2">Nama</th><th class="py-2">Tipe Kamar</th><th class="py-2 text-right">Harga</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($invoice->booking->jamaahPivot as $bj)
                            <tr><td class="py-2">{{ $bj->jamaah->full_name }}</td><td class="py-2">{{ $bj->room_type->label() }}</td><td class="py-2 text-right">{{ rupiah($bj->price) }}</td></tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-slate-200"><td colspan="2" class="py-2 font-medium">Total</td><td class="py-2 text-right font-bold text-brand-600">{{ rupiah($invoice->total_amount) }}</td></tr>
                    </tfoot>
                </table>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="mb-4 font-semibold text-slate-800">Riwayat Pembayaran</h3>
                <div class="divide-y divide-slate-100">
                    @forelse($invoice->payments as $payment)
                        <div class="flex items-center justify-between py-2 text-sm">
                            <div>
                                <p class="font-medium text-slate-700">{{ $payment->number }} &middot; {{ rupiah($payment->amount) }}</p>
                                <p class="text-xs text-slate-400">{{ $payment->type->label() }} &middot; {{ tanggal_id($payment->payment_date) }}</p>
                            </div>
                            <x-badge :color="$payment->status->color()">{{ $payment->status->label() }}</x-badge>
                        </div>
                    @empty
                        <p class="py-2 text-sm text-slate-400">Belum ada pembayaran.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="mb-4 font-semibold text-slate-800">Status Tagihan</h3>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">Status</dt><dd><x-badge :color="$invoice->status->color()">{{ $invoice->status->label() }}</x-badge></dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Terbit</dt><dd>{{ tanggal_id($invoice->issued_date) }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Jatuh Tempo</dt><dd>{{ tanggal_id($invoice->due_date) }}</dd></div>
                <div class="flex justify-between border-t border-slate-100 pt-2"><dt class="text-slate-500">Total</dt><dd class="font-semibold">{{ rupiah($invoice->total_amount) }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Terbayar</dt><dd class="text-emerald-600">{{ rupiah($invoice->paid_amount) }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Sisa</dt><dd class="font-bold text-rose-600">{{ rupiah($invoice->outstanding()) }}</dd></div>
            </dl>
        </div>
    </div>
@endsection
