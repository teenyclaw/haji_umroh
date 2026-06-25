@extends('layouts.app')
@section('title', 'Pembayaran')

@section('content')
    <x-page-header title="Pembayaran" subtitle="Verifikasi pembayaran dan kelola transaksi.">
        <x-slot:actions>
            <a href="{{ route('app.payments.create') }}" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">+ Catat Pembayaran</a>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" class="mb-4 flex flex-wrap gap-2">
        <select name="status" class="rounded-lg border-slate-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
            <option value="">Semua Status</option>
            @foreach($statuses as $key => $label)
                <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <button class="rounded-lg bg-slate-700 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Filter</button>
    </form>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Nomor</th>
                        <th class="px-5 py-3">Tanggal</th>
                        <th class="px-5 py-3">Paket</th>
                        <th class="px-5 py-3">Jenis</th>
                        <th class="px-5 py-3">Jumlah</th>
                        <th class="px-5 py-3">Bukti</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3 font-medium text-slate-800">{{ $payment->number }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ tanggal_id($payment->payment_date) }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $payment->invoice?->booking?->package?->name }}</td>
                            <td class="px-5 py-3">{{ $payment->type->label() }}</td>
                            <td class="px-5 py-3 font-medium">{{ rupiah($payment->amount) }}</td>
                            <td class="px-5 py-3">
                                @if($payment->proofUrl())
                                    <a href="{{ $payment->proofUrl() }}" target="_blank" class="text-brand-600 hover:underline">Lihat</a>
                                @else - @endif
                            </td>
                            <td class="px-5 py-3"><x-badge :color="$payment->status->color()">{{ $payment->status->label() }}</x-badge></td>
                            <td class="px-5 py-3 text-right">
                                @if($payment->status->value === 'pending')
                                    <div class="flex justify-end gap-1">
                                        <form method="POST" action="{{ route('app.payments.verify', $payment) }}">
                                            @csrf
                                            <button class="rounded bg-emerald-600 px-2 py-1 text-xs text-white hover:bg-emerald-700">Verifikasi</button>
                                        </form>
                                        <form method="POST" action="{{ route('app.payments.reject', $payment) }}" onsubmit="return confirm('Tolak pembayaran ini?')">
                                            @csrf
                                            <button class="rounded bg-rose-600 px-2 py-1 text-xs text-white hover:bg-rose-700">Tolak</button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400">{{ $payment->verified_at ? tanggal_id($payment->verified_at) : '-' }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-5 py-8 text-center text-slate-400">Belum ada pembayaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $payments->links() }}</div>
@endsection
