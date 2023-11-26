<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobtypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('jobtypes')->insert([
            'name' => 'Part-time',
        ]);

        // Full-time
        DB::table('jobtypes')->insert([
            'name' => 'Full-time',
        ]);

        // Magang
        DB::table('jobtypes')->insert([
            'name' => 'Magang',
        ]);
    }
}
