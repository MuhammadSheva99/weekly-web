<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpRekomendasi extends Model
{
    use HasUuids;

    protected $table = 'sp_rekomendasi';

    protected $fillable = [
        'user_id', 'direkomendasikan_oleh', 'level_usulan', 'alasan',
        'lampiran_path', 'status', 'alasan_penolakan', 'diproses_oleh', 'surat_peringatan_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function direkomendasikanOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'direkomendasikan_oleh');
    }

    public function diprosesOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    public function suratPeringatan(): BelongsTo
    {
        return $this->belongsTo(SuratPeringatan::class, 'surat_peringatan_id');
    }
}