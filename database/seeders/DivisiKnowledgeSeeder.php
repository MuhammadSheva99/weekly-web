<?php

namespace Database\Seeders;

use App\Models\DivisiKnowledge;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DivisiKnowledgeSeeder extends Seeder
{
    /**
     * Aman dijalankan berulang: data yang sudah ada (dan mungkin sudah diedit
     * HR/Supervisor) TIDAK ditimpa. Untuk memuat ulang satu divisi,
     * hapus barisnya lalu jalankan seeder lagi.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/divisi_knowledge.json');

        $items = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

        foreach ($items as $item) {
            DivisiKnowledge::firstOrCreate(
                ['slug' => $item['slug']],
                [
                    'nama_divisi' => $item['nama_divisi'],
                    'content'     => $item['content'],
                    'is_active'   => true,
                ]
            );
        }

        $this->linkToDivisiTable();

        DivisiKnowledge::flushCache();

        $this->command?->info('DivisiKnowledge: ' . DivisiKnowledge::count() . ' divisi tersimpan.');
    }

    /**
     * Opsional: isi divisi_id dengan mencocokkan nama_divisi di tabel divisi.
     * ASUMSI: model App\Models\Divisi dengan kolom id dan nama.
     * Jika berbeda, sesuaikan di sini. Jika gagal, service tetap jalan
     * karena pencarian juga bisa memakai nama divisi.
     */
    private function linkToDivisiTable(): void
    {
        $divisiClass = \App\Models\Divisi::class;

        if (! class_exists($divisiClass) || ! Schema::hasTable((new $divisiClass)->getTable())) {
            $this->command?->warn('Model/tabel Divisi tidak ditemukan, penautan divisi_id dilewati.');

            return;
        }

        try {
            $knownSlugs = DivisiKnowledge::pluck('slug')->all();

            foreach ($divisiClass::query()->get(['id', 'nama']) as $divisi) {
                $slug = DivisiKnowledge::matchSlug($divisi->nama, $knownSlugs);

                if ($slug === null) {
                    $this->command?->warn("Divisi \"{$divisi->nama}\" belum punya referensi.");

                    continue;
                }

                // Satu referensi hanya ditautkan ke satu divisi_id (yang pertama cocok).
                // Divisi lain dengan nama mirip tetap terlayani lewat pencocokan nama.
                DivisiKnowledge::where('slug', $slug)
                    ->whereNull('divisi_id')
                    ->update(['divisi_id' => $divisi->id]);
            }
        } catch (\Throwable $e) {
            $this->command?->warn('Penautan divisi_id dilewati: ' . $e->getMessage());
        }
    }
}