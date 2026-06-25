<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'manage-packages', 'manage-jamaah', 'manage-bookings',
            'manage-payments', 'verify-payments', 'manage-documents',
            'verify-documents', 'manage-rombongan', 'manage-manasik',
            'manage-agents', 'manage-commissions', 'view-reports',
            'manage-inquiries', 'manage-settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $roles = [
            'super-admin' => $permissions,
            'owner' => $permissions,
            'admin-operasional' => [
                'manage-packages', 'manage-jamaah', 'manage-bookings',
                'manage-documents', 'manage-rombongan', 'manage-manasik', 'manage-inquiries',
            ],
            'keuangan' => ['manage-payments', 'verify-payments', 'view-reports', 'manage-commissions'],
            'marketing' => ['manage-jamaah', 'manage-agents', 'manage-inquiries'],
            'dokumen' => ['manage-documents', 'verify-documents'],
            'manasik' => ['manage-manasik'],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($rolePermissions);
        }
    }
}
