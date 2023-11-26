<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UniversitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('universities')->insert([
            'name' => 'Universitas Internasional Semen Indonesia',
            'location' => 'Kompleks PT. Semen Indonesia (Persero) Tbk, Jl. Veteran, Kb. Dalem, Sidomoro, Kebomas, Gresik Regency, East Java 61122',
            'province' => 'JAWA TIMUR',
            'regency' => 'GRESIK',
            'district' => 'KEBOMAS',
        ]);

        // 2. Universitas Muhammadiyah Gresik
        DB::table('universities')->insert([
            'name' => 'Universitas Muhammadiyah Gresik',
            'location' => 'Jl. Sumatera No.101, Gn. Malang, Randuagung, Kec. Kebomas, Kabupaten Gresik, Jawa Timur 61121',
            'province' => 'JAWA TIMUR',
            'regency' => 'GRESIK',
            'district' => 'KEBOMAS',
        ]);

        // 3. Universitas Gresik
        DB::table('universities')->insert([
            'name' => 'Universitas Gresik',
            'location' => 'Jl. Arif Rahman Hakim Gresik No.2B, Kramatandap, Gapurosukolilo, Kec. Gresik, Kabupaten Gresik, Jawa Timur 61111',
            'province' => 'JAWA TIMUR',
            'regency' => 'GRESIK',
            'district' => 'GRESIK',
        ]);
    }
}
