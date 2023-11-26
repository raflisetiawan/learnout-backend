<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentroleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('student_roles')->insert([
            'name' => 'Mahasiswa',
        ]);

        // Alumni
        DB::table('student_roles')->insert([
            'name' => 'Alumni',
        ]);
    }
}
