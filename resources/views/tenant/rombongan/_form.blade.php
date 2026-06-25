@php $rom = $rombongan ?? null; @endphp
<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="grid gap-4 sm:grid-cols-2">
        <x-input label="Nama Rombongan" name="name" :value="$rom?->name" required />
        <x-select label="Paket" name="package_id" :selected="$rom?->package_id" placeholder="Tanpa paket" :options="$packages->pluck('name', 'id')" />
        <x-input label="Ketua Rombongan" name="leader_name" :value="$rom?->leader_name" />
        <x-input label="Muthowif / Pembimbing" name="muthowif_name" :value="$rom?->muthowif_name" />
        <x-input label="Nomor Bus" name="bus_number" :value="$rom?->bus_number" />
        <x-input label="Tanggal Berangkat" name="departure_date" type="date" :value="$rom?->departure_date?->toDateString()" />
        <div class="sm:col-span-2"><x-textarea label="Catatan" name="notes" :value="$rom?->notes" rows="2" /></div>
    </div>
</div>
