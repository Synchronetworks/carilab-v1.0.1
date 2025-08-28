<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\Models\User;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('12345678'),
            'mobile' => '1-81859861',
            'date_of_birth' => fake()->date,
            'gender' => 'Male',
            'email_verified_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'user_type' => 'admin',
            'address' => '3 Elim Street, USA',
            'is_subscribe' => 0,
            'degree' => '',
            'experience' => '',
            'image' => public_path('/dummy-images/profile/admin/super_admin.png'),
        ]);
    }
}
