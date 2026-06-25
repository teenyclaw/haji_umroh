@extends('layouts.app')
@section('title', 'Booking Baru')

@php
    $packageData = $packages->mapWithKeys(function ($p) {
        $extra = (float) $p->handling_fee + (float) $p->insurance_fee + (float) $p->visa_fee;
        $rooms = [];
        foreach (['quad','triple','double','single'] as $room) {
            $price = $p->prices->firstWhere('room_type', $room)?->price;
            $rooms[$room] = $price !== null ? (float) $price + $extra : (float) $p->base_price + $extra;
        }
        return [$p->id => $rooms];
    });
@endphp

@section('content')
    <x-page-header title="Buat Booking" subtitle="Pilih paket dan tambahkan jamaah peserta." />

    <form method="POST" action="{{ route('app.bookings.store') }}" class="max-w-4xl space-y-6"
          x-data="bookingForm(@js($packageData), '{{ $selectedPackage }}')">
        @csrf
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Paket <span class="text-rose-500">*</span></label>
                    <select name="package_id" x-model="packageId" required class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        <option value="">Pilih paket...</option>
                        @foreach($packages as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <x-select label="Agent / Referral" name="agent_id" placeholder="Tanpa agent" :options="$agents->pluck('name', 'id')" />
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="font-semibold text-slate-800">Peserta</h3>
                <button type="button" @click="addRow" class="rounded-lg bg-brand-50 px-3 py-1.5 text-sm font-medium text-brand-700 hover:bg-brand-100">+ Tambah Jamaah</button>
            </div>
            <div class="space-y-3">
                <template x-for="(row, index) in rows" :key="index">
                    <div class="flex flex-wrap items-end gap-2 rounded-lg border border-slate-100 bg-slate-50 p-3">
                        <div class="min-w-[200px] flex-1">
                            <label class="mb-1 block text-xs font-medium text-slate-600">Jamaah</label>
                            <select :name="`participants[${index}][jamaah_id]`" x-model="row.jamaah_id" required class="w-full rounded-lg border-slate-300 text-sm">
                                <option value="">Pilih jamaah...</option>
                                @foreach($jamaahList as $j)
                                    <option value="{{ $j->id }}">{{ $j->full_name }} @if($j->gender?->value === 'P')(P)@endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-40">
                            <label class="mb-1 block text-xs font-medium text-slate-600">Tipe Kamar</label>
                            <select :name="`participants[${index}][room_type]`" x-model="row.room_type" @change="recalc" required class="w-full rounded-lg border-slate-300 text-sm">
                                @foreach($roomTypes as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-36 text-right">
                            <label class="mb-1 block text-xs font-medium text-slate-600">Harga</label>
                            <p class="py-2 text-sm font-semibold text-slate-800" x-text="formatRupiah(priceOf(row.room_type))"></p>
                        </div>
                        <button type="button" @click="removeRow(index)" class="rounded-lg p-2 text-rose-500 hover:bg-rose-50" x-show="rows.length > 1">&times;</button>
                    </div>
                </template>
            </div>

            <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-4">
                <span class="text-sm text-slate-500">Total Estimasi</span>
                <span class="text-xl font-bold text-brand-600" x-text="formatRupiah(total)"></span>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <x-textarea label="Catatan" name="notes" rows="2" />
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('app.bookings.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</a>
            <button class="rounded-lg bg-brand-600 px-5 py-2 text-sm font-semibold text-white hover:bg-brand-700">Buat Booking</button>
        </div>
    </form>

    <script>
        function bookingForm(prices, preselect) {
            return {
                prices,
                packageId: preselect || '',
                rows: [{ jamaah_id: '', room_type: 'quad' }],
                total: 0,
                addRow() { this.rows.push({ jamaah_id: '', room_type: 'quad' }); this.recalc(); },
                removeRow(i) { this.rows.splice(i, 1); this.recalc(); },
                priceOf(room) {
                    if (!this.packageId || !this.prices[this.packageId]) return 0;
                    return this.prices[this.packageId][room] || 0;
                },
                recalc() { this.total = this.rows.reduce((sum, r) => sum + this.priceOf(r.room_type), 0); },
                formatRupiah(v) { return 'Rp ' + (v || 0).toLocaleString('id-ID'); },
                init() {
                    this.$watch('packageId', () => this.recalc());
                    this.$watch('rows', () => this.recalc(), { deep: true });
                    this.recalc();
                }
            }
        }
    </script>
@endsection
