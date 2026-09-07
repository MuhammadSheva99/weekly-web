<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DivisiSeeder::class,
            RoleSeeder::class,
            AdminSeeder::class,
            UserSeeder::class,
        ]);
    }
}