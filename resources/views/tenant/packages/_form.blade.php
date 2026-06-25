@php
    $pkg = $package ?? null;
    $priceFor = fn ($room) => $pkg?->prices->firstWhere('room_type', $room)?->price;
@endphp

<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <h3 class="mb-4 font-semibold text-slate-800">Informasi Umum</h3>
    <div class="grid gap-4 sm:grid-cols-2">
        <x-input label="Nama Paket" name="name" :value="$pkg?->name" required />
        <x-select label="Tipe Paket" name="type" :options="$types" :selected="$pkg?->type?->value" required />
        <x-input label="Kuota Jamaah" name="quota" type="number" :value="$pkg?->quota" />
        <x-input label="Durasi (hari)" name="duration_days" type="number" :value="$pkg?->duration_days" />
        <x-input label="Tanggal Berangkat" name="departure_date" type="date" :value="$pkg?->departure_date?->toDateString()" />
        <x-input label="Tanggal Pulang" name="return_date" type="date" :value="$pkg?->return_date?->toDateString()" />
        <div class="sm:col-span-2">
            <x-textarea label="Deskripsi" name="description" :value="$pkg?->description" rows="3" />
        </div>
    </div>
</div>

<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <h3 class="mb-4 font-semibold text-slate-800">Penerbangan & Hotel</h3>
    <div class="grid gap-4 sm:grid-cols-2">
        <x-input label="Maskapai" name="airline" :value="$pkg?->airline" />
        <x-input label="Bandara Keberangkatan" name="departure_airport" :value="$pkg?->departure_airport" />
        <x-input label="Hotel Makkah" name="hotel_makkah" :value="$pkg?->hotel_makkah" />
        <x-input label="Bintang Hotel Makkah" name="hotel_makkah_star" type="number" :value="$pkg?->hotel_makkah_star" />
        <x-input label="Jarak Hotel Makkah" name="hotel_makkah_distance" :value="$pkg?->hotel_makkah_distance" />
        <x-input label="Hotel Madinah" name="hotel_madinah" :value="$pkg?->hotel_madinah" />
        <x-input label="Bintang Hotel Madinah" name="hotel_madinah_star" type="number" :value="$pkg?->hotel_madinah_star" />
        <x-input label="Jarak Hotel Madinah" name="hotel_madinah_distance" :value="$pkg?->hotel_madinah_distance" />
    </div>
</div>

<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <h3 class="mb-4 font-semibold text-slate-800">Harga per Tipe Kamar</h3>
    <p class="mb-3 text-xs text-slate-500">Kosongkan jika tipe kamar tidak ditawarkan. Biaya tambahan akan dijumlahkan otomatis saat booking.</p>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach($roomTypes as $room)
            <x-input :label="$room->label()" :name="'prices[' . $room->value . ']'" type="number" :value="$priceFor($room->value)" />
        @endforeach
    </div>
    <h4 class="mb-3 mt-5 text-sm font-semibold text-slate-700">Biaya Tambahan</h4>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-input label="Harga Dasar" name="base_price" type="number" :value="$pkg?->base_price" />
        <x-input label="Handling Fee" name="handling_fee" type="number" :value="$pkg?->handling_fee" />
        <x-input label="Asuransi" name="insurance_fee" type="number" :value="$pkg?->insurance_fee" />
        <x-input label="Biaya Visa" name="visa_fee" type="number" :value="$pkg?->visa_fee" />
    </div>
</div>

<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <h3 class="mb-4 font-semibold text-slate-800">Lainnya</h3>
    <div class="grid gap-4 sm:grid-cols-2">
        <x-input label="Nomor PPIU / PIHK" name="ppiu_number" :value="$pkg?->ppiu_number" />
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Gambar Paket</label>
            <input type="file" name="image" accept="image/*" class="w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-sm file:text-brand-700">
        </div>
        <label class="flex items-center gap-2 text-sm text-slate-700">
            <input type="hidden" name="is_published" value="0">
            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $pkg?->is_published ?? true)) class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
            Tampilkan di portal publik
        </label>
        <label class="flex items-center gap-2 text-sm text-slate-700">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $pkg?->is_active ?? true)) class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
            Paket aktif
        </label>
    </div>
</div>
