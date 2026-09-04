<?php

namespace Database\Seeders;

use App\Models\Divisi;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/karyawan.json');

        if (! file_exists($path)) {
            $this->command->warn('File data/karyawan.json tidak ditemukan. Salin dari karyawan.example.json lalu isi data asli.');
            return;
        }

        $data = json_decode(file_get_contents($path), true);

        foreach ($data as $item) {
            $namaDivisi = trim($item['divisi'] ?? '');
            $namaRole = trim($item['role'] ?? '');

            // Kalau divisi kosong, divisi_id otomatis null (tidak error)
            $divisiId = $namaDivisi !== ''
                ? Divisi::where('nama', $namaDivisi)->first()?->id
                : null;

            $roleId = Role::where('nama', $namaRole)->first()?->id;

            if (! $roleId) {
                $this->command->error("Data '{$item['nama']}' dilewati: role '{$namaRole}' tidak ditemukan.");
                continue;
            }

            User::firstOrCreate(
                ['email' => $item['email']],
                [
                    'nama' => $item['nama'],
                    'password' => Hash::make('password123'),
                    'divisi_id' => $divisiId,
                    'role_id' => $roleId,
                    'jabatan' => $item['jabatan'] ?? null,
                    'is_active' => true,
                ]
            );
        }

        foreach ($data as $item) {
            if (! empty($item['atasan'])) {
                $user = User::where('email', $item['email'])->first();
                $atasan = User::where('nama', $item['atasan'])->first();
                $user?->update(['atasan_id' => $atasan?->id]);
            }
        }

        $this->command->info(count($data).' karyawan berhasil diproses.');
    }
}