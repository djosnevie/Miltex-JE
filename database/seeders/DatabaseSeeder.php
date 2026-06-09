<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Device;
use App\Models\PointOfSale;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create a Default Tenant
        $tenant = Tenant::firstOrCreate(
            ['subdomain' => 'miltex'],
            ['name' => 'Miltex EAJE Tenant', 'status' => 'active']
        );

        // 2. Create the Company associated with this Tenant
        Company::firstOrCreate(
            ['nif' => 'A2609384F'],
            [
                'tenant_id'      => $tenant->id,
                'name'           => 'M I L T E X   S A R L',
                'rccm'           => 'CD/KIN/RCCM/26-B-00123',
                'address_street' => '12 Route des Poids Lourds',
                'address_city'   => 'Kinshasa/Limete',
                'email'          => 'contact@miltex.cd',
                'phone'          => '+243810000000',
            ]
        );

        // 3. Create a Point of Sale
        $pos = PointOfSale::firstOrCreate(
            ['location_identifier' => 'DEP-01'],
            [
                'company_id' => Company::where('nif', 'A2609384F')->first()->id,
                'name'       => 'Dépôt Principal Kinshasa',
                'city'       => 'Kinshasa',
            ]
        );

        // 4. Create the target Device
        Device::firstOrCreate(
            ['nid' => 'IC02000193-1'],
            [
                'point_of_sale_id' => $pos->id,
                'isf'              => '102030405',
                'model'            => 'Incotex 133',
                'firmware_version' => '1.2.0',
            ]
        );

        // 5. Create users with roles
        $users = [
            [
                'name'      => 'Administrateur Miltex',
                'email'     => 'admin@miltex.cd',
                'password'  => Hash::make('password'),
                'role'      => 'super_admin',
                'tenant_id' => null,           // super_admin has no tenant restriction
                'is_active' => true,
            ],
            [
                'name'      => 'Manager EAJE',
                'email'     => 'manager@miltex.cd',
                'password'  => Hash::make('password'),
                'role'      => 'admin',
                'tenant_id' => $tenant->id,
                'is_active' => true,
            ],
            [
                'name'      => 'Analyste Fiscal',
                'email'     => 'analyste@miltex.cd',
                'password'  => Hash::make('password'),
                'role'      => 'analyst',
                'tenant_id' => $tenant->id,
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
