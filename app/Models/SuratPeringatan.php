<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratPeringatan extends Model
{
    use HasUuids;

    protected $table = 'surat_peringatan';

    protected $fillable = [
        'no_sp', 'user_id', 'level', 'alasan', 'konsekuensi',
        'tanggal_terbit', 'tanggal_berakhir', 'diterbitkan_oleh', 'file_path',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_terbit' => 'date',
            'tanggal_berakhir' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function diterbitkanOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diterbitkan_oleh');
    }

    public function isAktif(): bool
    {
        return $this->tanggal_berakhir->isFuture();
    }
}