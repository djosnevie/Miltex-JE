<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $miltexTenant = Tenant::where('subdomain', 'miltex')->first();
        $retailTenant = Tenant::where('subdomain', 'crd-retail')->first();
        $pharmaTenant = Tenant::where('subdomain', 'pharma-fleuve')->first();

        $users = [
            // 1. Super Administrateur Global
            [
                'name'      => 'Administrateur Miltex',
                'email'     => 'admin@miltex.cd',
                'password'  => Hash::make('password'),
                'role'      => 'super_admin',
                'tenant_id' => null,
                'is_active' => true,
            ],
            // 2. Administrateur Miltex EAJE
            [
                'name'      => 'Manager EAJE',
                'email'     => 'manager@miltex.cd',
                'password'  => Hash::make('password'),
                'role'      => 'admin',
                'tenant_id' => $miltexTenant?->id,
                'is_active' => true,
            ],
            // 3. Analyste Fiscal Miltex EAJE
            [
                'name'      => 'Analyste Fiscal',
                'email'     => 'analyste@miltex.cd',
                'password'  => Hash::make('password'),
                'role'      => 'analyst',
                'tenant_id' => $miltexTenant?->id,
                'is_active' => true,
            ],
            // 4. Gérant Congo Retail Distribution
            [
                'name'      => 'Gérant Congo Retail',
                'email'     => 'gerant@crd-retail.cd',
                'password'  => Hash::make('password'),
                'role'      => 'admin',
                'tenant_id' => $retailTenant?->id,
                'is_active' => true,
            ],
            // 5. Responsable Pharmacie du Fleuve
            [
                'name'      => 'Pharmacien Responsable',
                'email'     => 'direction@pharma-fleuve.cd',
                'password'  => Hash::make('password'),
                'role'      => 'admin',
                'tenant_id' => $pharmaTenant?->id,
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
