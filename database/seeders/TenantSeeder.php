<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tenant Principal Miltex EAJE
        $miltexTenant = Tenant::updateOrCreate(
            ['subdomain' => 'miltex'],
            [
                'name'               => 'Miltex EAJE Tenant',
                'status'             => 'active',
                'max_points_of_sale' => 15,
            ]
        );

        Company::updateOrCreate(
            ['nif' => 'A2609384F'],
            [
                'tenant_id'          => $miltexTenant->id,
                'name'               => 'M I L T E X   S A R L',
                'rccm'               => 'CD/KIN/RCCM/26-B-00123',
                'address_street'     => '12 Route des Poids Lourds',
                'address_city'       => 'Kinshasa/Limete',
                'email'              => 'contact@miltex.cd',
                'phone'              => '+243810000000',
                'max_points_of_sale' => 10,
            ]
        );

        // 2. Tenant Démo 2 : Congo Retail Distribution
        $retailTenant = Tenant::updateOrCreate(
            ['subdomain' => 'crd-retail'],
            [
                'name'               => 'Congo Retail Distribution',
                'status'             => 'active',
                'max_points_of_sale' => 5,
            ]
        );

        Company::updateOrCreate(
            ['nif' => 'A1904582K'],
            [
                'tenant_id'          => $retailTenant->id,
                'name'               => 'CRD Supermarchés SARL',
                'rccm'               => 'CD/KIN/RCCM/19-B-04582',
                'address_street'     => '45 Boulevard du 30 Juin',
                'address_city'       => 'Kinshasa/Gombe',
                'email'              => 'direction@crd-retail.cd',
                'phone'              => '+243820000111',
                'max_points_of_sale' => 5,
            ]
        );

        // 3. Tenant Démo 3 : Pharmacies du Fleuve
        $pharmaTenant = Tenant::updateOrCreate(
            ['subdomain' => 'pharma-fleuve'],
            [
                'name'               => 'Pharmacies du Fleuve',
                'status'             => 'active',
                'max_points_of_sale' => 3,
            ]
        );

        Company::updateOrCreate(
            ['nif' => 'A2108743M'],
            [
                'tenant_id'          => $pharmaTenant->id,
                'name'               => 'Pharmacie Centrale du Fleuve',
                'rccm'               => 'CD/KIN/RCCM/21-B-08743',
                'address_street'     => '88 Avenue Colonel Mondjiba',
                'address_city'       => 'Kinshasa/Ngaliema',
                'email'              => 'contact@pharma-fleuve.cd',
                'phone'              => '+243890000222',
                'max_points_of_sale' => 3,
            ]
        );
    }
}
