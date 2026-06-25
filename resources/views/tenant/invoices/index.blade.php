@extends('layouts.app')
@section('title', 'Invoice')

@section('content')
    <x-page-header title="Invoice" subtitle="Daftar tagihan jamaah." />

    <form method="GET" class="mb-4 flex flex-wrap gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor invoice..."
               class="w-full max-w-xs rounded-lg border-slate-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
        <select name="status" class="rounded-lg border-slate-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
            <option value="">Semua Status</option>
            <option value="unpaid" @selected(request('status')==='unpaid')>Belum Dibayar</option>
            <option value="partial" @selected(request('status')==='partial')>Sebagian</option>
            <option value="paid" @selected(request('status')==='paid')>Lunas</option>
        </select>
        <button class="rounded-lg bg-slate-700 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Filter</button>
    </form>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Nomor</th>
                        <th class="px-5 py-3">Paket</th>
                        <th class="px-5 py-3">Total</th>
                        <th class="px-5 py-3">Terbayar</th>
                        <th class="px-5 py-3">Sisa</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3 font-medium text-slate-800">{{ $invoice->number }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $invoice->booking?->package?->name }}</td>
                            <td class="px-5 py-3">{{ rupiah($invoice->total_amount) }}</td>
                            <td class="px-5 py-3 text-emerald-600">{{ rupiah($invoice->paid_amount) }}</td>
                            <td class="px-5 py-3 text-rose-600">{{ rupiah($invoice->outstanding()) }}</td>
                            <td class="px-5 py-3"><x-badge :color="$invoice->status->color()">{{ $invoice->status->label() }}</x-badge></td>
                            <td class="px-5 py-3 text-right"><a href="{{ route('app.invoices.show', $invoice) }}" class="text-brand-600 hover:underline">Detail</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-8 text-center text-slate-400">Belum ada invoice.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $invoices->links() }}</div>
@endsection
