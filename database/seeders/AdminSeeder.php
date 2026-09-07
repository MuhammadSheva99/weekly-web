<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $roleAdmin = Role::where('nama', 'Admin')->first();

        if (! $roleAdmin) {
            $this->command->error('Role Admin belum ada. Jalankan RoleSeeder dulu.');
            return;
        }

        User::firstOrCreate(
            ['email' => config('admin.email')],
            [
                'nama' => 'Administrator',
                'password' => Hash::make(config('admin.password')),
                'role_id' => $roleAdmin->id,
                'divisi_id' => null,
                'jabatan' => 'System Administrator',
                'is_active' => true,
            ]
        );

        $this->command->info('Akun admin siap: '.config('admin.email'));
    }
}