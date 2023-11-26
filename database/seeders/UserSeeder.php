<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'student',
            'email' => 'student@gmail.com',
            'password' => Hash::make('password'),
            'role_id' => 1, // Assume 'user' role_id is 1
        ]);

        // Company Seeder
        DB::table('users')->insert([
            'name' => 'Company',
            'email' => 'company@gmail.com',
            'password' => Hash::make('password'),
            'role_id' => 2, // Assume 'company' role_id is 2
        ]);

        // Admin Seeder
        DB::table('users')->insert([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role_id' => 3, // Assume 'admin' role_id is 3
        ]);
    }
}
