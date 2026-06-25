@php $a = $agent ?? null; @endphp
<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="grid gap-4 sm:grid-cols-2">
        <x-input label="Nama Agent" name="name" :value="$a?->name" required />
        <x-input label="Kode Referral" name="code" :value="$a?->code" placeholder="Otomatis jika kosong" />
        <x-input label="No. HP" name="phone" :value="$a?->phone" />
        <x-input label="Email" name="email" type="email" :value="$a?->email" />
        <x-select label="Tipe Komisi" name="commission_type" :options="$commissionTypes" :selected="$a?->commission_type?->value" required />
        <x-input label="Nilai Komisi" name="commission_value" type="number" :value="$a?->commission_value" required />
        <x-input label="Nama Bank" name="bank_name" :value="$a?->bank_name" />
        <x-input label="No. Rekening" name="bank_account_number" :value="$a?->bank_account_number" />
        <div class="sm:col-span-2"><x-textarea label="Alamat" name="address" :value="$a?->address" rows="2" /></div>
        <label class="flex items-center gap-2 text-sm text-slate-700">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $a?->is_active ?? true)) class="rounded border-slate-300 text-brand-600 focus:ring-brand-500"> Agent aktif
        </label>
    </div>
</div>
