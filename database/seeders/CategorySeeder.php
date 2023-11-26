<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            'name' => 'Tutor',
        ]);

        // Kategori 2
        DB::table('categories')->insert([
            'name' => 'Digital Marketing',
        ]);

        // Kategori 3
        DB::table('categories')->insert([
            'name' => 'Kasir',
        ]);
    }
}
