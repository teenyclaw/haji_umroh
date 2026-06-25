@php $j = $jamaah ?? null; @endphp

<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <h3 class="mb-4 font-semibold text-slate-800">Identitas</h3>
    <div class="grid gap-4 sm:grid-cols-2">
        <x-input label="Nama Lengkap (sesuai paspor)" name="full_name" :value="$j?->full_name" required />
        <x-input label="NIK (16 digit)" name="nik" :value="$j?->nik" />
        <x-input label="Tempat Lahir" name="birth_place" :value="$j?->birth_place" />
        <x-input label="Tanggal Lahir" name="birth_date" type="date" :value="$j?->birth_date?->toDateString()" />
        <x-select label="Jenis Kelamin" name="gender" :options="$genders" :selected="$j?->gender?->value" required />
        <x-select label="Status Pernikahan" name="marital_status" :selected="$j?->marital_status" placeholder="Pilih..."
                  :options="['Belum Menikah' => 'Belum Menikah', 'Menikah' => 'Menikah', 'Cerai' => 'Cerai']" />
    </div>
</div>

<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <h3 class="mb-4 font-semibold text-slate-800">Kontak & Alamat</h3>
    <div class="grid gap-4 sm:grid-cols-2">
        <x-input label="No. HP" name="phone" :value="$j?->phone" />
        <x-input label="WhatsApp" name="whatsapp" :value="$j?->whatsapp" />
        <x-input label="Email" name="email" type="email" :value="$j?->email" />
        <x-input label="Kota" name="city" :value="$j?->city" />
        <x-input label="Provinsi" name="province" :value="$j?->province" />
        <div class="sm:col-span-2"><x-textarea label="Alamat Lengkap" name="address" :value="$j?->address" rows="2" /></div>
    </div>
</div>

<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <h3 class="mb-4 font-semibold text-slate-800">Paspor</h3>
    <div class="grid gap-4 sm:grid-cols-2">
        <x-input label="Nomor Paspor" name="passport_number" :value="$j?->passport_number" />
        <x-input label="Kantor Penerbit" name="passport_office" :value="$j?->passport_office" />
        <x-input label="Tanggal Terbit" name="passport_issued_at" type="date" :value="$j?->passport_issued_at?->toDateString()" />
        <x-input label="Tanggal Kedaluwarsa" name="passport_expired_at" type="date" :value="$j?->passport_expired_at?->toDateString()" />
    </div>
</div>

<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <h3 class="mb-4 font-semibold text-slate-800">Mahram & Lainnya</h3>
    <p class="mb-3 text-xs text-slate-500">Data mahram wajib untuk jamaah perempuan (jika validasi mahram diaktifkan di Pengaturan).</p>
    <div class="grid gap-4 sm:grid-cols-2">
        <x-input label="Hubungan Mahram" name="mahram_relation" :value="$j?->mahram_relation" placeholder="contoh: Suami, Ayah" />
        <x-input label="Nama Mahram" name="mahram_name" :value="$j?->mahram_name" />
        <x-input label="Kontak Darurat (Nama)" name="emergency_name" :value="$j?->emergency_name" />
        <x-input label="Kontak Darurat (No. HP)" name="emergency_phone" :value="$j?->emergency_phone" />
        <x-input label="Ukuran Baju" name="shirt_size" :value="$j?->shirt_size" placeholder="S/M/L/XL" />
        <x-input label="Nomor Porsi Haji" name="porsi_number" :value="$j?->porsi_number" />
        <x-select label="Agent / Referral" name="agent_id" :selected="$j?->agent_id" placeholder="Tanpa agent"
                  :options="$agents->pluck('name', 'id')" />
        <x-select label="Status" name="status" :options="$statuses" :selected="$j?->status?->value ?? 'prospek'" required />
        <div class="sm:col-span-2"><x-textarea label="Catatan Kesehatan" name="health_notes" :value="$j?->health_notes" rows="2" /></div>
        <div class="sm:col-span-2"><x-textarea label="Catatan" name="notes" :value="$j?->notes" rows="2" /></div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Pas Foto</label>
            <input type="file" name="photo" accept="image/*" class="w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-sm file:text-brand-700">
        </div>
    </div>
</div>
