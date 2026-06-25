@extends('layouts.app')
@section('title', 'Komisi')

@section('content')
    <x-page-header title="Komisi Agent" subtitle="Kelola pembayaran komisi agent." />

    <div class="mb-6 grid gap-4 sm:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Komisi Belum Dibayar</p><p class="mt-2 text-2xl font-bold text-amber-600">{{ rupiah($summary['pending']) }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Komisi Sudah Dibayar</p><p class="mt-2 text-2xl font-bold text-emerald-600">{{ rupiah($summary['paid']) }}</p></div>
    </div>

    <form method="GET" class="mb-4">
        <select name="status" onchange="this.form.submit()" class="rounded-lg border-slate-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
            <option value="">Semua Status</option>
            <option value="pending" @selected(request('status')==='pending')>Belum Dibayar</option>
            <option value="paid" @selected(request('status')==='paid')>Sudah Dibayar</option>
        </select>
    </form>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                <tr><th class="px-5 py-3">Agent</th><th class="px-5 py-3">Booking</th><th class="px-5 py-3">Jumlah</th><th class="px-5 py-3">Status</th><th class="px-5 py-3 text-right">Aksi</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($commissions as $commission)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3 font-medium text-slate-800">{{ $commission->agent->name }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ $commission->booking?->code ?? '-' }}</td>
                        <td class="px-5 py-3 font-medium">{{ rupiah($commission->amount) }}</td>
                        <td class="px-5 py-3"><x-badge :color="$commission->status === 'paid' ? 'emerald' : 'amber'">{{ $commission->status === 'paid' ? 'Dibayar' : 'Pending' }}</x-badge></td>
                        <td class="px-5 py-3 text-right">
                            @if($commission->status !== 'paid')
                                <form method="POST" action="{{ route('app.commissions.pay', $commission) }}">@csrf<button class="rounded bg-emerald-600 px-2 py-1 text-xs text-white hover:bg-emerald-700">Tandai Dibayar</button></form>
                            @else
                                <span class="text-xs text-slate-400">{{ tanggal_id($commission->paid_date) }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-slate-400">Belum ada komisi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $commissions->links() }}</div>
@endsection
