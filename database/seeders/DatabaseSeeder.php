<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Device;
use App\Models\PointOfSale;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create a Default Tenant
        $tenant = Tenant::create([
            'name' => 'Miltex EAJE Tenant',
            'subdomain' => 'miltex',
            'status' => 'active',
        ]);

        // 2. Create the Company associated with this Tenant
        $company = Company::create([
            'tenant_id' => $tenant->id,
            'name' => 'M I L T E X   S A R L',
            'nif' => 'A2609384F',
            'rccm' => 'CD/KIN/RCCM/26-B-00123',
            'address_street' => '12 Route des Poids Lourds',
            'address_city' => 'Kinshasa/Limete',
            'email' => 'contact@miltex.cd',
            'phone' => '+243810000000',
        ]);

        // 3. Create a Point of Sale
        $pos = PointOfSale::create([
            'company_id' => $company->id,
            'name' => 'Dépôt Principal Kinshasa',
            'location_identifier' => 'DEP-01',
            'city' => 'Kinshasa',
        ]);

        // 4. Create the target Device (NID matching JE_IC02000193-1_1532.txt)
        Device::create([
            'point_of_sale_id' => $pos->id,
            'nid' => 'IC02000193-1',
            'isf' => '102030405',
            'model' => 'Incotex 133',
            'firmware_version' => '1.2.0',
        ]);

        // 5. Create default admin user
        User::factory()->create([
            'name' => 'Administrateur Miltex',
            'email' => 'admin@miltex.cd',
            'password' => bcrypt('password'), // standard dev password
        ]);
    }
}
