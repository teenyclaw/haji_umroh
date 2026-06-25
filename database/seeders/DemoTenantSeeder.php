<?php

namespace Database\Seeders;

use App\Enums\Gender;
use App\Enums\JamaahStatus;
use App\Enums\PackageType;
use App\Enums\PaymentType;
use App\Enums\RoomType;
use App\Models\Agent;
use App\Models\Jamaah;
use App\Models\ManasikSchedule;
use App\Models\Package;
use App\Models\Payment;
use App\Models\User;
use App\Services\BookingService;
use App\Services\DocumentChecklistService;
use App\Services\PaymentService;
use App\Services\TenantProvisioner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoTenantSeeder extends Seeder
{
    public function run(): void
    {
        $provisioner = app(TenantProvisioner::class);
        $bookingService = app(BookingService::class);
        $paymentService = app(PaymentService::class);
        $checklist = app(DocumentChecklistService::class);

        $tenant = \App\Models\Tenant::where('slug', 'barokah')->first();

        if (! $tenant) {
            $tenant = $provisioner->provision([
                'name' => 'Barokah Tour & Travel',
                'slug' => 'barokah',
                'phone' => '081234567890',
                'email' => 'cs@barokahtour.test',
                'city' => 'Surabaya',
                'owner_name' => 'H. Abdullah',
                'owner_email' => 'owner@barokahtour.test',
                'owner_password' => 'password',
            ]);
        }

        $tenant->update([
            'province' => 'Jawa Timur',
            'whatsapp' => '081234567890',
            'bank_name' => 'Bank Syariah Indonesia',
            'bank_account_number' => '7001234567',
            'bank_account_holder' => 'PT Barokah Tour Travel',
            'izin_number' => 'SK Kemenag No. 123 Tahun 2024',
        ]);

        tenancy()->set($tenant);

        foreach ([
            ['name' => 'Siti Aminah', 'email' => 'keuangan@barokahtour.test', 'role' => 'keuangan'],
            ['name' => 'Budi Santoso', 'email' => 'admin@barokahtour.test', 'role' => 'admin-operasional'],
        ] as $staff) {
            $user = User::updateOrCreate(
                ['email' => $staff['email']],
                [
                    'tenant_id' => $tenant->id,
                    'name' => $staff['name'],
                    'password' => Hash::make('password'),
                    'is_active' => true,
                ]
            );
            $user->syncRoles([$staff['role']]);
        }

        $agent = Agent::firstOrCreate(
            ['tenant_id' => $tenant->id, 'code' => 'AGEN01'],
            ['name' => 'Ustadz Hidayat', 'phone' => '0819988776', 'commission_type' => 'flat', 'commission_value' => 1000000, 'is_active' => true]
        );

        $umroh = Package::firstOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Umroh Reguler Februari 2026'],
            [
                'type' => PackageType::Umroh,
                'description' => 'Umroh 9 hari dengan penerbangan langsung dan hotel dekat Masjidil Haram.',
                'quota' => 45,
                'duration_days' => 9,
                'departure_date' => now()->addMonths(2)->toDateString(),
                'return_date' => now()->addMonths(2)->addDays(9)->toDateString(),
                'airline' => 'Saudia Airlines',
                'departure_airport' => 'Surabaya (SUB)',
                'hotel_makkah' => 'Hilton Suites Makkah',
                'hotel_makkah_star' => 5,
                'hotel_makkah_distance' => '150 m dari Masjidil Haram',
                'hotel_madinah' => 'Anwar Al Madinah Movenpick',
                'hotel_madinah_star' => 5,
                'hotel_madinah_distance' => '100 m dari Masjid Nabawi',
                'base_price' => 28000000,
                'handling_fee' => 1500000,
                'insurance_fee' => 500000,
                'visa_fee' => 1500000,
                'is_published' => true,
                'is_active' => true,
            ]
        );

        foreach ([
            RoomType::Quad->value => 28000000,
            RoomType::Triple->value => 30000000,
            RoomType::Double->value => 33000000,
        ] as $room => $price) {
            $umroh->prices()->updateOrCreate(['room_type' => $room], ['price' => $price]);
        }

        Package::firstOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Haji Khusus 1447 H'],
            [
                'type' => PackageType::HajiKhusus,
                'description' => 'Paket haji khusus dengan fasilitas premium dan bimbingan intensif.',
                'quota' => 20,
                'duration_days' => 26,
                'departure_date' => now()->addMonths(8)->toDateString(),
                'airline' => 'Garuda Indonesia',
                'hotel_makkah' => 'Fairmont Makkah Clock Royal Tower',
                'hotel_makkah_star' => 5,
                'hotel_madinah' => 'The Oberoi Madinah',
                'hotel_madinah_star' => 5,
                'base_price' => 165000000,
                'is_published' => true,
                'is_active' => true,
            ]
        );

        $samples = [
            ['Ahmad Fauzi', Gender::L, null, null],
            ['Muhammad Rizki', Gender::L, null, null],
            ['Fatimah Zahra', Gender::P, 'Suami', 'Ahmad Fauzi'],
            ['Khadijah Putri', Gender::P, 'Ayah Kandung', 'Slamet Riyadi'],
            ['Abdul Rahman', Gender::L, null, null],
        ];

        $createdJamaah = [];
        foreach ($samples as $i => [$name, $gender, $mahramRel, $mahramName]) {
            $jamaah = Jamaah::firstOrCreate(
                ['tenant_id' => $tenant->id, 'full_name' => $name],
                [
                    'agent_id' => $agent->id,
                    'nik' => '35780112340000' . str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT),
                    'birth_place' => 'Surabaya',
                    'birth_date' => now()->subYears(35 + $i)->toDateString(),
                    'gender' => $gender,
                    'marital_status' => 'Menikah',
                    'phone' => '0812000000' . $i,
                    'whatsapp' => '0812000000' . $i,
                    'city' => 'Surabaya',
                    'province' => 'Jawa Timur',
                    'passport_number' => 'C' . rand(1000000, 9999999),
                    'passport_expired_at' => now()->addYears(3)->toDateString(),
                    'mahram_relation' => $mahramRel,
                    'mahram_name' => $mahramName,
                    'status' => JamaahStatus::Prospek,
                ]
            );
            $checklist->syncForJamaah($jamaah, $umroh);
            $createdJamaah[] = $jamaah;
        }

        if ($umroh->bookings()->count() === 0) {
            $booking = $bookingService->create(
                $umroh,
                [
                    ['jamaah_id' => $createdJamaah[0]->id, 'room_type' => RoomType::Quad->value],
                    ['jamaah_id' => $createdJamaah[2]->id, 'room_type' => RoomType::Quad->value],
                ],
                $agent->id,
                'Booking demo pasangan suami istri.'
            );

            $payment = Payment::create([
                'tenant_id' => $tenant->id,
                'invoice_id' => $booking->invoice->id,
                'number' => $bookingService->generateCode('PAY'),
                'payment_date' => now()->toDateString(),
                'amount' => 10000000,
                'type' => PaymentType::Dp,
                'method' => 'transfer',
                'bank_name' => 'Bank Syariah Indonesia',
                'status' => 'pending',
            ]);

            $paymentService->verify($payment);
        }

        ManasikSchedule::firstOrCreate(
            ['tenant_id' => $tenant->id, 'title' => 'Manasik Umroh Tahap 1'],
            [
                'package_id' => $umroh->id,
                'scheduled_at' => now()->addMonth()->setTime(8, 0),
                'location' => 'Aula Barokah Tour, Surabaya',
                'instructor' => 'Ustadz Hidayat',
                'material' => 'Tata cara umroh, miqat, dan ihram.',
            ]
        );

        tenancy()->forget();
    }
}
