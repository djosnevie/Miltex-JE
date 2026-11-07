<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Device;
use App\Models\PointOfSale;
use Illuminate\Database\Seeder;

class PointOfSaleSeeder extends Seeder
{
    public function run(): void
    {
        $miltexCompany = Company::where('nif', 'A2609384F')->first();
        $crdCompany = Company::where('nif', 'A1904582K')->first();

        // 1. Points de vente Miltex
        if ($miltexCompany) {
            $pos1 = PointOfSale::updateOrCreate(
                ['location_identifier' => 'DEP-01'],
                [
                    'company_id' => $miltexCompany->id,
                    'name'       => 'Dépôt Principal Kinshasa',
                    'city'       => 'Kinshasa',
                ]
            );

            Device::updateOrCreate(
                ['nid' => 'IC02000193-1'],
                [
                    'point_of_sale_id' => $pos1->id,
                    'isf'              => '102030405',
                    'model'            => 'Incotex 133',
                    'firmware_version' => '1.2.0',
                ]
            );

            $pos2 = PointOfSale::updateOrCreate(
                ['location_identifier' => 'LSH-01'],
                [
                    'company_id' => $miltexCompany->id,
                    'name'       => 'Agence Commerciale Lubumbashi',
                    'city'       => 'Lubumbashi',
                ]
            );

            Device::updateOrCreate(
                ['nid' => 'IC02000244-2'],
                [
                    'point_of_sale_id' => $pos2->id,
                    'isf'              => '203040506',
                    'model'            => 'Incotex 133',
                    'firmware_version' => '1.2.0',
                ]
            );
        }

        // 2. Points de vente Congo Retail Distribution
        if ($crdCompany) {
            $pos3 = PointOfSale::updateOrCreate(
                ['location_identifier' => 'GMB-01'],
                [
                    'company_id' => $crdCompany->id,
                    'name'       => 'Hypermarché Gombe Centre',
                    'city'       => 'Kinshasa/Gombe',
                ]
            );

            Device::updateOrCreate(
                ['nid' => 'IC02000388-1'],
                [
                    'point_of_sale_id' => $pos3->id,
                    'isf'              => '304050607',
                    'model'            => 'Incotex 133',
                    'firmware_version' => '1.3.1',
                ]
            );
        }
    }
}
