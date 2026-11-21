<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('user_role')->insert([
            'id' => Str::uuid()->toString(),
            'name' => "Super Admin",
            'landing_page' => 'Admin/Dashboard',
            'created_by_id' => '0',
            'status' => '1'
        ]);

        DB::table('user_role')->insert([
            'id' => Str::uuid()->toString(),
            'name' => "Admin",
            'code' => "A",
            'landing_page' => 'Admin/Dashboard',
            'created_by_id' => '0',
            'status' => '1'
        ]);

        DB::table('user_role')->insert([
            'id' => Str::uuid()->toString(),
            'name' => "Profile",
            'code' => "P",
            'landing_page' => 'Admin/Dashboard',
            'created_by_id' => '0',
            'status' => '1'
        ]);

        DB::table('user_role')->insert([
            'id' => Str::uuid()->toString(),
            'name' => "Agent",
            'code' => "AG",
            'landing_page' => 'Admin/Dashboard',
            'created_by_id' => '0',
            'status' => '1'
        ]);
    }
}
