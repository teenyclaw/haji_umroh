# Sistem Informasi Travel Haji & Umroh (Multi-Tenant SaaS)

Aplikasi manajemen travel haji dan umroh berbasis **Laravel 13 + MySQL + Blade + Tailwind CSS + Alpine.js**, dirancang sebagai **multi-tenant SaaS** sehingga satu instalasi dapat dipakai oleh banyak travel sekaligus, dengan isolasi data per travel.

## Fitur Utama

| Modul | Keterangan |
|-------|-----------|
| Super Admin (Platform) | Onboarding travel baru, kelola status, reset kata sandi owner |
| Dashboard Travel | KPI jamaah, booking, piutang, pembayaran & dokumen pending, paspor kedaluwarsa |
| Paket | Haji/Umroh, kuota, hotel, maskapai, harga per tipe kamar (Quad/Triple/Double/Single), biaya tambahan |
| Jamaah | Data lengkap (NIK, paspor, mahram, kontak darurat), status pipeline, foto |
| Booking | Multi-jamaah per booking, perhitungan harga otomatis, validasi mahram |
| Keuangan | Invoice, pembayaran DP/cicilan/pelunasan, verifikasi transfer + bukti, PDF invoice |
| Dokumen | Checklist per tipe paket (haji vs umroh), upload & verifikasi berkas |
| Rombongan | Kelompok keberangkatan, alokasi anggota dari booking |
| Manasik | Jadwal manasik + absensi jamaah |
| Agent & Komisi | Referral agent, komisi flat/persentase otomatis, pembayaran komisi |
| Laporan | Ringkasan keuangan, grafik kas masuk, export Excel (jamaah & pembayaran) |
| Portal Publik | Landing page per travel (`/t/{slug}`) dengan katalog paket + form inquiry |

## Kebutuhan Sistem

- PHP >= 8.3 (teruji pada 8.4)
- MySQL >= 8.0
- Composer 2.x
- Laragon (disarankan untuk Windows)

## Instalasi (Laragon)

1. Letakkan project di `C:\laragon\www\haji_umroh` (sudah sesuai).
2. Buat database MySQL bernama `haji_umroh`.
3. Salin `.env` (sudah disediakan) dan sesuaikan kredensial database bila perlu.
4. Install dependency dan siapkan aplikasi:

```bash
composer install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```

5. Akses melalui Laragon (`http://haji_umroh.test`) atau jalankan server bawaan:

```bash
php artisan serve
```

### Catatan Composer di balik proxy/SSL inspection

Jika `composer install` gagal dengan error `curl error 60 ... unable to get local issuer certificate`, jaringan Anda melakukan SSL inspection. Solusinya gunakan CA bundle dari Windows certificate store:

```powershell
$env:COMPOSER_CAFILE="C:\laragon\www\haji_umroh\_winca.pem"
composer install
```

Berkas `_winca.pem` berisi sertifikat root dari mesin Anda dan sudah masuk `.gitignore`.

## Akun Default (setelah seeding)

| Peran | Email | Kata Sandi |
|-------|-------|-----------|
| Super Admin Platform | `superadmin@hajiumroh.test` | `password` |
| Owner Travel (demo) | `owner@barokahtour.test` | `password` |
| Keuangan (demo) | `keuangan@barokahtour.test` | `password` |
| Admin Operasional (demo) | `admin@barokahtour.test` | `password` |

Portal publik travel demo: `http://haji_umroh.test/t/barokah`

## Arsitektur Multi-Tenant

- Satu database bersama; setiap tabel bisnis memiliki kolom `tenant_id`.
- Trait [`app/Models/Concerns/BelongsToTenant.php`](app/Models/Concerns/BelongsToTenant.php) menambahkan **global scope** otomatis dan mengisi `tenant_id` saat pembuatan record.
- Middleware [`app/Http/Middleware/ResolveTenant.php`](app/Http/Middleware/ResolveTenant.php) menetapkan tenant aktif dari user yang login; [`ResolvePublicTenant`](app/Http/Middleware/ResolvePublicTenant.php) menetapkannya dari slug untuk portal publik.
- Urutan middleware diatur di [`bootstrap/app.php`](bootstrap/app.php) agar tenant di-resolve **sebelum** route-model binding, sehingga akses lintas-tenant otomatis menghasilkan 404.

```
Request -> Authenticate -> ResolveTenant -> SubstituteBindings -> Controller
```

## Peran & Hak Akses

Dikelola dengan Spatie Laravel Permission. Peran tersedia: `super-admin`, `owner`, `admin-operasional`, `keuangan`, `marketing`, `dokumen`, `manasik`. Definisi izin ada di [`database/seeders/RolePermissionSeeder.php`](database/seeders/RolePermissionSeeder.php).

## Struktur Routing

| Prefix | Untuk | Middleware |
|--------|-------|-----------|
| `/admin` | Super admin platform | `auth`, `superadmin` |
| `/app` | Dashboard travel (per tenant) | `auth`, `tenant` |
| `/t/{slug}` | Portal publik travel | `public.tenant` |

## Onboarding Travel Baru

1. Masuk sebagai Super Admin.
2. Buka menu **Travel / Tenant** lalu **+ Travel Baru**.
3. Isi data travel dan akun owner. Sistem otomatis membuat:
   - Record tenant
   - User owner dengan peran `owner`
   - Daftar jenis dokumen standar (KTP, KK, Paspor, dll.)
   Lihat [`app/Services/TenantProvisioner.php`](app/Services/TenantProvisioner.php).
4. Owner dapat login di `/login` dan langsung mengelola travelnya.

## Logika Bisnis Inti

- [`app/Services/BookingService.php`](app/Services/BookingService.php): membuat booking multi-jamaah, invoice, dan komisi agent.
- [`app/Services/PaymentService.php`](app/Services/PaymentService.php): verifikasi/penolakan pembayaran, rekalkulasi status invoice & status jamaah.
- [`app/Services/DocumentChecklistService.php`](app/Services/DocumentChecklistService.php): sinkronisasi checklist dokumen sesuai tipe paket.

## Konvensi Indonesia

- Validasi NIK 16 digit, format rupiah (`Rp 15.000.000`), tanggal lokal Indonesia.
- Validasi mahram untuk jamaah perempuan saat booking (dapat diaktif/nonaktifkan di Pengaturan travel).
- Field opsional regulasi: nomor PPIU/PIHK dan nomor porsi haji.

## Pengembangan Lanjutan (Roadmap)

- Notifikasi WhatsApp (Fonnte/Wablas)
- Payment gateway (Midtrans/Xendit)
- Custom domain per tenant
- Modul tabungan haji/umroh
- API untuk aplikasi mobile

## Tampilan Front-End

Admin panel memakai Tailwind CSS dan Alpine.js via CDN sehingga aplikasi langsung berjalan tanpa proses build. Untuk produksi, Anda dapat memindahkannya ke pipeline Vite (`npm install && npm run build`) sesuai kebutuhan.
