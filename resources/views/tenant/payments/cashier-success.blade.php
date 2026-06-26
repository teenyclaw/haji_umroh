@extends('layouts.app')
@section('title', 'Transaksi Berhasil')

@section('content')
    <div class="mx-auto max-w-2xl">
        <div class="rounded-2xl border border-emerald-200 bg-white p-8 text-center shadow-sm">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-3xl text-emerald-600">✓</div>
            <h1 class="text-2xl font-bold text-slate-800">Pembayaran Berhasil</h1>
            <p class="mt-2 text-slate-500">Transaksi kasir telah dicatat dan diverifikasi.</p>

            <div class="mt-6 rounded-xl bg-slate-50 p-5 text-left text-sm">
                <dl class="space-y-2">
                    <div class="flex justify-between"><dt class="text-slate-500">No. Kwitansi</dt><dd class="font-semibold">{{ $payment->number }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Invoice</dt><dd>{{ $payment->invoice->number }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Jamaah</dt><dd class="text-right">{{ $payment->invoice->booking?->jamaahPivot->map(fn ($bj) => $bj->jamaah->full_name)->join(', ') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Paket</dt><dd>{{ $payment->invoice->booking?->package?->name }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Jenis</dt><dd>{{ $payment->type->label() }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Metode</dt><dd>{{ ucfirst($payment->method) }}</dd></div>
                    <div class="flex justify-between border-t border-slate-200 pt-2"><dt class="font-medium text-slate-700">Jumlah Dibayar</dt><dd class="text-lg font-bold text-brand-600">{{ rupiah($payment->amount) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Sisa Tagihan</dt><dd>{{ rupiah($payment->invoice->outstanding()) }}</dd></div>
                </dl>
            </div>

            <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-center">
                <a href="{{ route('app.payments.receipt', $payment) }}"
                   id="print-receipt"
                   target="_blank"
                   class="rounded-xl bg-brand-600 px-6 py-3 text-sm font-semibold text-white hover:bg-brand-700">
                    Cetak Kwitansi
                </a>
                <a href="{{ route('app.payments.cashier') }}"
                   class="rounded-xl border border-slate-300 px-6 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Transaksi Baru
                </a>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        window.addEventListener('load', () => {
            document.getElementById('print-receipt')?.click();
        });
    </script>
    @endpush
@endsection
