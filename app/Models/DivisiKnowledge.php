<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class DivisiKnowledge extends Model
{
    public const CACHE_KEY = 'ai_coach:divisi_knowledge';

    protected $table = 'divisi_knowledge';

    protected $fillable = [
        'divisi_id',
        'slug',
        'nama_divisi',
        'content',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Alias penulisan nama divisi (sudah dalam bentuk slug) => slug resmi.
     * Tambahkan di sini jika nama divisi di tabel Anda berbeda penulisannya.
     */
    private const ALIASES = [
        'conten-creator'      => 'content-creator',
        'hrga'                => 'hr-ga',
        'hr-dan-ga'           => 'hr-ga',
        'hr-and-ga'           => 'hr-ga',
        'hr'                  => 'hr-ga',
        'programmer'          => 'ai-support',
        'programer'           => 'ai-support',
        'admin-stock'         => 'admin-stock-warehouse',
        'stock-warehouse'     => 'admin-stock-warehouse',
        'marketing-many-platforms' => 'marketing-many-platform',
    ];

    protected static function booted(): void
    {
        // Setiap kali HR/Supervisor mengubah data, cache langsung dibersihkan.
        static::saved(fn () => static::flushCache());
        static::deleted(fn () => static::flushCache());
    }

    public static function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Ubah nama divisi bebas menjadi slug resmi (memakai alias bila ada).
     */
    public static function normalizeSlug(?string $namaDivisi): string
    {
        $slug = Str::slug((string) $namaDivisi);

        return self::ALIASES[$slug] ?? $slug;
    }

    /**
     * Cocokkan nama divisi dengan daftar slug yang tersedia.
     * Urutan: cocok persis -> slug terpanjang yang terkandung di nama divisi.
     * Contoh: "AI Support and programer" -> "ai-support".
     *
     * @param  array<int, string>  $knownSlugs
     */
    public static function matchSlug(?string $namaDivisi, array $knownSlugs): ?string
    {
        $slug = self::normalizeSlug($namaDivisi);

        if ($slug === '') {
            return null;
        }

        if (in_array($slug, $knownSlugs, true)) {
            return $slug;
        }

        $best = null;
        foreach ($knownSlugs as $known) {
            if (str_contains($slug, $known) && ($best === null || strlen($known) > strlen($best))) {
                $best = $known;
            }
        }

        return $best;
    }

    /**
     * Ambil teks referensi untuk satu divisi.
     * Prioritas: divisi_id -> kecocokan nama divisi. Mengembalikan '' bila tidak ada.
     */
    public static function contentFor(?string $divisiId, ?string $namaDivisi): string
    {
        $ttl = (int) config('ai_coach.knowledge_cache_ttl', 3600);

        /** @var array<int, array{divisi_id: string|null, slug: string, content: string}> $rows */
        $rows = Cache::remember(self::CACHE_KEY, $ttl, function () {
            return static::query()
                ->where('is_active', true)
                ->get(['divisi_id', 'slug', 'content'])
                ->toArray();
        });

        if ($divisiId) {
            foreach ($rows as $row) {
                if (! empty($row['divisi_id']) && (string) $row['divisi_id'] === $divisiId) {
                    return (string) $row['content'];
                }
            }
        }

        $slug = self::matchSlug($namaDivisi, array_column($rows, 'slug'));

        if ($slug !== null) {
            foreach ($rows as $row) {
                if ($row['slug'] === $slug) {
                    return (string) $row['content'];
                }
            }
        }

        return '';
    }
}