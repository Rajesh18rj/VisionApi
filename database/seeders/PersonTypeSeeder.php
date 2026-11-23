<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PersonTypeSeeder extends Seeder
{
    public function run(): void
    {
        $personTypes = [
            [
                'name' => 'Unknown',
                'rgb_code' => '235, 52, 52',
                'hex_code' => '#eb3434',
            ],
            [
                'name' => 'Lead',
                'rgb_code' => '235, 116, 52',
                'hex_code' => '#eb7434',
            ],
            [
                'name' => 'Customer',
                'rgb_code' => '3, 163, 3',
                'hex_code' => '#03a303',
            ],
            [
                'name' => 'Vender',
                'rgb_code' => '210, 52, 235',
                'hex_code' => '#d234eb',
            ],
            [
                'name' => 'Sub-Contractor',
                'rgb_code' => '64, 52, 235',
                'hex_code' => '#4034eb',
            ],
            [
                'name' => 'Distributor',
                'rgb_code' => '235, 52, 147',
                'hex_code' => '#eb3493',
            ],
            [
                'name' => 'Management Team',
                'rgb_code' => '52, 201, 235',
                'hex_code' => '#34c9eb',
            ],
            [
                'name' => 'Employee',
                'rgb_code' => '232, 235, 52',
                'hex_code' => '#e8eb34',
            ],
            [
                'name' => 'Contact Employee',
                'rgb_code' => '153, 179, 7',
                'hex_code' => '#99b307',
            ],
            [
                'name' => 'Staff',
                'rgb_code' => '52, 235, 156',
                'hex_code' => '#34eb9c',
            ],
            [
                'name' => 'Supporting Staff',
                'rgb_code' => '7, 181, 106',
                'hex_code' => '#07b56a',
            ],
            [
                'name' => 'Driver',
                'rgb_code' => '111, 44, 212',
                'hex_code' => '#6f2cd4',
            ],
        ];

        foreach ($personTypes as $personType) {
            DB::table('person_type')->updateOrInsert(
                ['name' => $personType['name']],
                [
                    'id' => (string) Str::uuid(),
                    'rgb_code' => $personType['rgb_code'],
                    'hex_code' => $personType['hex_code'],
                    'created_by_id' => '0',

                ]
            );
        }
    }
}