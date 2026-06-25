@extends('layouts.app')
@section('title', 'Booking')

@section('content')
    <x-page-header title="Booking" subtitle="Daftar pemesanan paket oleh jamaah.">
        <x-slot:actions>
            <a href="{{ route('app.bookings.create') }}" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">+ Booking Baru</a>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" class="mb-4 flex flex-wrap gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode booking..."
               class="w-full max-w-xs rounded-lg border-slate-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
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
                        <th class="px-5 py-3">Kode</th>
                        <th class="px-5 py-3">Paket</th>
                        <th class="px-5 py-3">Jamaah</th>
                        <th class="px-5 py-3">Total</th>
                        <th class="px-5 py-3">Invoice</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($bookings as $booking)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3 font-medium text-slate-800">{{ $booking->code }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $booking->package?->name }}</td>
                            <td class="px-5 py-3">{{ $booking->jamaah_pivot_count }} org</td>
                            <td class="px-5 py-3 font-medium">{{ rupiah($booking->total_amount) }}</td>
                            <td class="px-5 py-3">
                                @if($booking->invoice)
                                    <x-badge :color="$booking->invoice->status->color()">{{ $booking->invoice->status->label() }}</x-badge>
                                @else - @endif
                            </td>
                            <td class="px-5 py-3"><x-badge :color="$booking->status->color()">{{ $booking->status->label() }}</x-badge></td>
                            <td class="px-5 py-3 text-right"><a href="{{ route('app.bookings.show', $booking) }}" class="text-brand-600 hover:underline">Detail</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-8 text-center text-slate-400">Belum ada booking.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $bookings->links() }}</div>
@endsection
