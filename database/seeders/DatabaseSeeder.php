<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call(CategorySeeder::class);
        $this->call(JobtypeSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(StudentroleSeeder::class);
        $this->call(UniversitySeeder::class);
        $this->call(UserSeeder::class);
        // $this->call(UserTableSeeder::class);
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
