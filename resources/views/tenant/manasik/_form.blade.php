@php $m = $manasik ?? null; @endphp
<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="grid gap-4 sm:grid-cols-2">
        <x-input label="Judul Manasik" name="title" :value="$m?->title" required />
        <x-select label="Paket" name="package_id" :selected="$m?->package_id" placeholder="Umum (semua paket)" :options="$packages->pluck('name', 'id')" />
        <x-input label="Waktu" name="scheduled_at" type="datetime-local" :value="$m?->scheduled_at?->format('Y-m-d\TH:i')" required />
        <x-input label="Lokasi" name="location" :value="$m?->location" />
        <x-input label="Instruktur / Pembimbing" name="instructor" :value="$m?->instructor" />
        <div class="sm:col-span-2"><x-textarea label="Materi" name="material" :value="$m?->material" rows="3" /></div>
    </div>
</div>
