<?php

namespace Database\Seeders;

use App\Http\Model\user_role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user_role = user_role::where('name', 'Super Admin')->first();
        DB::table('user')->insert([
            'id' => Str::uuid()->toString(),
            'first_name' => "Demo",
            'last_name' => "Project",
            'user_role_id' => $user_role['id'],
            'password' => Crypt::encryptString('123456'),
            'confirm_password' => Crypt::encryptString('123456'),
            'email' => 'sadmin@gmail.com',
            'created_by_id' => '0',
            'status' => '1'
        ]);

        DB::table('user')->insert([
            'id' => "b5cec1c6-8783-4e60-b88b-d49d8ae658a7",
            'first_name' => "Demo",
            'last_name' => "Project",
            'user_role_id' => $user_role['id'],
            'password' => Crypt::encryptString('123456'),
            'confirm_password' => Crypt::encryptString('123456'),
            'email' => 'admin@gmail.com',
            'created_by_id' => '0',
            'status' => '1'
        ]);
    }
}
