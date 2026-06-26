@extends('layouts.app')
@section('title', 'Kasir')

@section('content')
    <x-page-header title="Kasir" subtitle="Pembayaran tunai/QRIS langsung terverifikasi & cetak kwitansi.">
        <x-slot:actions>
            <a href="{{ route('app.payments.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Daftar Pembayaran</a>
        </x-slot:actions>
    </x-page-header>

    <div
        x-data="cashierPos({
            invoices: @js($invoices),
            types: @js($types),
            selectedId: @js($selectedInvoice),
        })"
        class="grid gap-6 lg:grid-cols-5"
    >
        <div class="space-y-4 lg:col-span-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <label class="mb-2 block text-sm font-medium text-slate-700">Cari Invoice</label>
                <input
                    type="text"
                    x-model="search"
                    placeholder="Nomor invoice, paket, atau nama jamaah..."
                    class="mb-3 w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500"
                >
                <div class="max-h-80 space-y-2 overflow-y-auto">
                    <template x-for="invoice in filteredInvoices" :key="invoice.id">
                        <button
                            type="button"
                            @click="selectInvoice(invoice)"
                            class="w-full rounded-xl border p-3 text-left transition"
                            :class="selected?.id === invoice.id ? 'border-brand-500 bg-brand-50' : 'border-slate-200 hover:border-brand-300 hover:bg-slate-50'"
                        >
                            <p class="font-semibold text-slate-800" x-text="invoice.number"></p>
                            <p class="text-xs text-slate-500" x-text="invoice.package"></p>
                            <p class="mt-1 text-xs text-slate-600" x-text="invoice.jamaah"></p>
                            <p class="mt-2 text-sm font-medium text-brand-600" x-text="'Sisa ' + formatRupiah(invoice.outstanding)"></p>
                        </button>
                    </template>
                    <p x-show="filteredInvoices.length === 0" class="py-6 text-center text-sm text-slate-400">Tidak ada invoice ditemukan.</p>
                </div>
            </div>

            <div x-show="selected" x-cloak class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
                <h3 class="mb-3 font-semibold text-emerald-900">Detail Tagihan</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-emerald-700">Total</dt><dd class="font-medium" x-text="formatRupiah(selected?.total)"></dd></div>
                    <div class="flex justify-between"><dt class="text-emerald-700">Terbayar</dt><dd x-text="formatRupiah(selected?.paid)"></dd></div>
                    <div class="flex justify-between border-t border-emerald-200 pt-2"><dt class="font-semibold text-emerald-800">Sisa</dt><dd class="font-bold text-emerald-900" x-text="formatRupiah(selected?.outstanding)"></dd></div>
                </dl>
            </div>
        </div>

        <form method="POST" action="{{ route('app.payments.cashier.store') }}" class="lg:col-span-3">
            @csrf
            <input type="hidden" name="invoice_id" :value="selected?.id ?? ''">

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="mb-4 text-lg font-semibold text-slate-800">Input Pembayaran</h3>

                @if($errors->any())
                    <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div x-show="!selected" class="rounded-xl border border-dashed border-slate-300 bg-slate-50 py-12 text-center text-sm text-slate-500">
                    Pilih invoice di panel kiri untuk memulai transaksi.
                </div>

                <div x-show="selected" x-cloak class="space-y-4">
                    <div class="rounded-xl bg-slate-50 p-4">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Jamaah</p>
                        <p class="font-medium text-slate-800" x-text="selected?.jamaah"></p>
                        <p class="mt-1 text-sm text-slate-500" x-text="selected?.package"></p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Tanggal</label>
                            <input type="date" name="payment_date" value="{{ old('payment_date', now()->toDateString()) }}" required
                                   class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Jenis Pembayaran</label>
                            <select name="type" x-model="paymentType" required
                                    class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                @foreach($types as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="mb-1 block text-sm font-medium text-slate-700">Jumlah (Rp)</label>
                            <div class="flex gap-2">
                                <input type="number" name="amount" x-model="amount" min="1" :max="selected?.outstanding" required
                                       class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                <button type="button" @click="setFullAmount()"
                                        class="shrink-0 rounded-lg border border-brand-300 bg-brand-50 px-3 text-xs font-medium text-brand-700 hover:bg-brand-100">
                                    Lunas
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-slate-500">Maksimal: <span x-text="formatRupiah(selected?.outstanding)"></span></p>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Metode</label>
                            <select name="method" required
                                    class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                <option value="tunai" @selected(old('method', 'tunai') === 'tunai')>Tunai</option>
                                <option value="qris" @selected(old('method') === 'qris')>QRIS</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Catatan</label>
                            <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Opsional"
                                   class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                    </div>

                    <div class="rounded-xl border border-brand-200 bg-brand-50 p-4">
                        <p class="text-sm text-brand-800">Pembayaran akan langsung <strong>terverifikasi</strong> dan kwitansi siap dicetak.</p>
                    </div>

                    <button type="submit" :disabled="!selected || !amount"
                            class="w-full rounded-xl bg-brand-600 py-3 text-sm font-bold text-white hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-50">
                        Bayar &amp; Cetak Kwitansi
                    </button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function cashierPos({ invoices, types, selectedId }) {
            return {
                invoices,
                types,
                search: '',
                selected: invoices.find(i => i.id === selectedId) ?? null,
                amount: '',
                paymentType: 'dp',
                get filteredInvoices() {
                    const q = this.search.toLowerCase().trim();
                    if (!q) return this.invoices;
                    return this.invoices.filter(i =>
                        i.number.toLowerCase().includes(q) ||
                        (i.package ?? '').toLowerCase().includes(q) ||
                        (i.jamaah ?? '').toLowerCase().includes(q)
                    );
                },
                selectInvoice(invoice) {
                    this.selected = invoice;
                    this.amount = invoice.outstanding;
                    if (invoice.paid <= 0) {
                        this.paymentType = 'dp';
                    } else {
                        this.paymentType = 'pelunasan';
                    }
                },
                setFullAmount() {
                    if (this.selected) {
                        this.amount = this.selected.outstanding;
                        this.paymentType = 'pelunasan';
                    }
                },
                formatRupiah(value) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(value ?? 0));
                },
            };
        }
    </script>
    @endpush
@endsection
