<?php

namespace App\Services;

use App\Models\DocumentType;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TenantProvisioner
{
    public const DEFAULT_DOCUMENT_TYPES = [
        ['name' => 'KTP', 'code' => 'ktp', 'required_umroh' => true, 'required_haji' => true],
        ['name' => 'Kartu Keluarga', 'code' => 'kk', 'required_umroh' => true, 'required_haji' => true],
        ['name' => 'Paspor', 'code' => 'paspor', 'required_umroh' => true, 'required_haji' => true],
        ['name' => 'Pas Foto 4x6', 'code' => 'foto', 'required_umroh' => true, 'required_haji' => true],
        ['name' => 'Buku Nikah / Akta Lahir', 'code' => 'nikah', 'required_umroh' => true, 'required_haji' => true],
        ['name' => 'Sertifikat Vaksin Meningitis', 'code' => 'meningitis', 'required_umroh' => true, 'required_haji' => true],
        ['name' => 'Surat Keterangan Mahram', 'code' => 'mahram', 'required_umroh' => true, 'required_haji' => true],
        ['name' => 'Visa', 'code' => 'visa', 'required_umroh' => true, 'required_haji' => true],
    ];

    public function provision(array $data): Tenant
    {
        return DB::transaction(function () use ($data) {
            $tenant = Tenant::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
                'whatsapp' => $data['phone'] ?? null,
                'city' => $data['city'] ?? null,
                'is_active' => true,
            ]);

            $owner = User::create([
                'tenant_id' => $tenant->id,
                'name' => $data['owner_name'],
                'email' => $data['owner_email'],
                'password' => Hash::make($data['owner_password']),
                'is_super_admin' => false,
                'is_active' => true,
            ]);

            $owner->assignRole('owner');

            $sort = 0;
            foreach (self::DEFAULT_DOCUMENT_TYPES as $type) {
                DocumentType::create(array_merge($type, [
                    'tenant_id' => $tenant->id,
                    'is_active' => true,
                    'sort_order' => $sort++,
                ]));
            }

            return $tenant;
        });
    }
}
