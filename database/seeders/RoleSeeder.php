<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['Karyawan', 'Atasan', 'HRD', 'Management', 'Admin'];

        foreach ($roles as $nama) {
            Role::firstOrCreate(['nama' => $nama]);
        }
    }
}