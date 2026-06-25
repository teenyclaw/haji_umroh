@extends('layouts.app')
@section('title', 'Catat Pembayaran')

@section('content')
    <x-page-header title="Catat Pembayaran" subtitle="Catat pembayaran DP, cicilan, atau pelunasan." />

    <form method="POST" action="{{ route('app.payments.store') }}" enctype="multipart/form-data" class="max-w-2xl space-y-6">
        @csrf
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-slate-700">Invoice <span class="text-rose-500">*</span></label>
                    <select name="invoice_id" required class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        <option value="">Pilih invoice...</option>
                        @foreach($invoices as $invoice)
                            <option value="{{ $invoice->id }}" @selected((string)$selectedInvoice === (string)$invoice->id)>
                                {{ $invoice->number }} - {{ $invoice->booking?->package?->name }} (Sisa {{ rupiah($invoice->outstanding()) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <x-input label="Tanggal Pembayaran" name="payment_date" type="date" :value="now()->toDateString()" required />
                <x-input label="Jumlah (Rp)" name="amount" type="number" required />
                <x-select label="Jenis Pembayaran" name="type" :options="$types" required />
                <x-select label="Metode" name="method" :options="['transfer' => 'Transfer Bank', 'tunai' => 'Tunai', 'qris' => 'QRIS']" selected="transfer" required />
                <x-input label="Nama Bank" name="bank_name" />
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-slate-700">Bukti Pembayaran</label>
                    <input type="file" name="proof" accept="image/*" class="w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-sm file:text-brand-700">
                </div>
                <div class="sm:col-span-2"><x-textarea label="Catatan" name="notes" rows="2" /></div>
            </div>
        </div>
        <div class="flex justify-end gap-2">
            <a href="{{ route('app.payments.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</a>
            <button class="rounded-lg bg-brand-600 px-5 py-2 text-sm font-semibold text-white hover:bg-brand-700">Simpan Pembayaran</button>
        </div>
    </form>
@endsection
