@extends('layouts.app')
@section('title', 'Agent')

@section('content')
    <x-page-header title="Agent / Mitra" subtitle="Kelola agent referral dan komisinya.">
        <x-slot:actions>
            <a href="{{ route('app.agents.create') }}" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">+ Agent Baru</a>
        </x-slot:actions>
    </x-page-header>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                <tr><th class="px-5 py-3">Nama</th><th class="px-5 py-3">Kode</th><th class="px-5 py-3">Skema Komisi</th><th class="px-5 py-3">Booking</th><th class="px-5 py-3">Total Komisi</th><th class="px-5 py-3">Status</th><th class="px-5 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($agents as $agent)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3 font-medium text-slate-800">{{ $agent->name }}</td>
                        <td class="px-5 py-3 text-slate-500">{{ $agent->code }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ $agent->commission_type->label() === 'Persentase (dari harga paket)' ? $agent->commission_value . '%' : rupiah($agent->commission_value) }}</td>
                        <td class="px-5 py-3">{{ $agent->bookings_count }}</td>
                        <td class="px-5 py-3">{{ rupiah($agent->total_commission ?? 0) }}</td>
                        <td class="px-5 py-3"><x-badge :color="$agent->is_active ? 'emerald' : 'slate'">{{ $agent->is_active ? 'Aktif' : 'Off' }}</x-badge></td>
                        <td class="px-5 py-3 text-right"><a href="{{ route('app.agents.show', $agent) }}" class="text-brand-600 hover:underline">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-8 text-center text-slate-400">Belum ada agent.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $agents->links() }}</div>
@endsection
