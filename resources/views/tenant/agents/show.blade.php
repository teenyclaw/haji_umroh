@extends('layouts.app')
@section('title', $agent->name)

@section('content')
    <x-page-header :title="$agent->name" :subtitle="'Kode: ' . $agent->code">
        <x-slot:actions>
            <a href="{{ route('app.agents.edit', $agent) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Edit</a>
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="mb-4 font-semibold text-slate-800">Informasi</h3>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">HP</dt><dd>{{ $agent->phone ?? '-' }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Email</dt><dd>{{ $agent->email ?? '-' }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Komisi</dt><dd>{{ $agent->commission_type->label() }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Nilai</dt><dd class="font-medium">{{ $agent->commission_type->value === 'percentage' ? $agent->commission_value . '%' : rupiah($agent->commission_value) }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Bank</dt><dd>{{ $agent->bank_name ?? '-' }} {{ $agent->bank_account_number }}</dd></div>
            </dl>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
            <h3 class="mb-4 font-semibold text-slate-800">Riwayat Komisi</h3>
            <div class="divide-y divide-slate-100">
                @forelse($agent->commissions as $commission)
                    <div class="flex items-center justify-between py-2 text-sm">
                        <div>
                            <p class="font-medium text-slate-700">{{ $commission->booking?->code ?? '-' }}</p>
                            <p class="text-xs text-slate-400">{{ $commission->booking?->package?->name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-slate-800">{{ rupiah($commission->amount) }}</p>
                            <x-badge :color="$commission->status === 'paid' ? 'emerald' : 'amber'">{{ ucfirst($commission->status) }}</x-badge>
                        </div>
                    </div>
                @empty
                    <p class="py-2 text-sm text-slate-400">Belum ada komisi.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
