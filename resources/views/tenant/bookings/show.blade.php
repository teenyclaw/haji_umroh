@extends('layouts.app')
@section('title', $booking->code)

@section('content')
    <x-page-header :title="'Booking ' . $booking->code" :subtitle="$booking->package?->name">
        <x-slot:actions>
            @if($booking->invoice)
                <a href="{{ route('app.invoices.show', $booking->invoice) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Lihat Invoice</a>
                <a href="{{ route('app.payments.create', ['invoice_id' => $booking->invoice->id]) }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Catat Pembayaran</a>
            @endif
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="mb-4 font-semibold text-slate-800">Peserta ({{ $booking->jamaahPivot->count() }})</h3>
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="text-left text-xs uppercase text-slate-500">
                        <tr><th class="py-2">Nama</th><th class="py-2">Tipe Kamar</th><th class="py-2">Rombongan</th><th class="py-2 text-right">Harga</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($booking->jamaahPivot as $bj)
                            <tr>
                                <td class="py-2"><a href="{{ route('app.jamaah.show', $bj->jamaah) }}" class="font-medium text-brand-600 hover:underline">{{ $bj->jamaah->full_name }}</a></td>
                                <td class="py-2">{{ $bj->room_type->label() }}</td>
                                <td class="py-2 text-slate-500">{{ $bj->rombongan?->name ?? '-' }}</td>
                                <td class="py-2 text-right font-medium">{{ rupiah($bj->price) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($booking->invoice)
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="mb-4 font-semibold text-slate-800">Pembayaran</h3>
                    <div class="divide-y divide-slate-100">
                        @forelse($booking->invoice->payments as $payment)
                            <div class="flex items-center justify-between py-2 text-sm">
                                <div>
                                    <p class="font-medium text-slate-700">{{ $payment->type->label() }} &middot; {{ rupiah($payment->amount) }}</p>
                                    <p class="text-xs text-slate-400">{{ tanggal_id($payment->payment_date) }} &middot; {{ $payment->method }}</p>
                                </div>
                                <x-badge :color="$payment->status->color()">{{ $payment->status->label() }}</x-badge>
                            </div>
                        @empty
                            <p class="py-2 text-sm text-slate-400">Belum ada pembayaran.</p>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="mb-4 font-semibold text-slate-800">Ringkasan</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-slate-500">Status</dt><dd><x-badge :color="$booking->status->color()">{{ $booking->status->label() }}</x-badge></dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Tanggal</dt><dd>{{ tanggal_id($booking->booking_date) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Agent</dt><dd>{{ $booking->agent?->name ?? '-' }}</dd></div>
                    <div class="flex justify-between border-t border-slate-100 pt-2"><dt class="font-medium text-slate-700">Total</dt><dd class="font-bold text-brand-600">{{ rupiah($booking->total_amount) }}</dd></div>
                    @if($booking->invoice)
                        <div class="flex justify-between"><dt class="text-slate-500">Terbayar</dt><dd class="text-emerald-600">{{ rupiah($booking->invoice->paid_amount) }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Sisa</dt><dd class="text-rose-600">{{ rupiah($booking->invoice->outstanding()) }}</dd></div>
                    @endif
                </dl>
            </div>

            @if($booking->commission)
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="mb-2 font-semibold text-slate-800">Komisi Agent</h3>
                    <p class="text-sm text-slate-500">{{ $booking->commission->agent->name }}</p>
                    <p class="mt-1 text-lg font-bold text-slate-800">{{ rupiah($booking->commission->amount) }}</p>
                    <x-badge :color="$booking->commission->status === 'paid' ? 'emerald' : 'amber'">{{ ucfirst($booking->commission->status) }}</x-badge>
                </div>
            @endif

            @if($booking->notes)
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="mb-2 font-semibold text-slate-800">Catatan</h3>
                    <p class="text-sm text-slate-600">{{ $booking->notes }}</p>
                </div>
            @endif
        </div>
    </div>
@endsection
