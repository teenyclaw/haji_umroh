@extends('layouts.app')
@section('title', 'Inquiry')

@section('content')
    <x-page-header title="Inquiry / Prospek" subtitle="Permintaan informasi dari portal publik." />

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                <tr><th class="px-5 py-3">Nama</th><th class="px-5 py-3">Kontak</th><th class="px-5 py-3">Paket</th><th class="px-5 py-3">Pesan</th><th class="px-5 py-3">Status</th><th class="px-5 py-3 text-right">Aksi</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($inquiries as $inquiry)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3 font-medium text-slate-800">{{ $inquiry->name }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ $inquiry->phone }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ $inquiry->package?->name ?? '-' }}</td>
                        <td class="px-5 py-3 max-w-xs truncate text-slate-500">{{ $inquiry->message ?? '-' }}</td>
                        <td class="px-5 py-3"><x-badge :color="$inquiry->status === 'baru' ? 'blue' : 'emerald'">{{ ucfirst($inquiry->status) }}</x-badge></td>
                        <td class="px-5 py-3 text-right">
                            <form method="POST" action="{{ route('app.inquiries.convert', $inquiry) }}" class="inline">
                                @csrf
                                <button class="rounded bg-brand-600 px-2 py-1 text-xs text-white hover:bg-brand-700">Jadikan Jamaah</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-slate-400">Belum ada inquiry.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $inquiries->links() }}</div>
@endsection
