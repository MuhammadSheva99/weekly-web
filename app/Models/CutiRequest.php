<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CutiRequest extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id', 'jenis_cuti', 'tanggal_mulai', 'tanggal_selesai',
        'jumlah_hari', 'keterangan', 'lampiran_path', 'disetujui_oleh',
        'status', 'alasan_penolakan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function disetujuiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }
}