<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CutiRequest extends Model
{
    use HasUuids;

    public const JENIS_BEBAS_KUOTA_JIKA_ADA_LAMPIRAN = ['Cuti sakit', 'Cuti melahirkan'];

    protected $fillable = [
        'user_id', 'jenis_cuti', 'tanggal_mulai', 'tanggal_selesai',
        'jumlah_hari', 'keterangan', 'lampiran_path', 'disetujui_oleh',
        'atasan_approved_by', 'atasan_approved_at',
        'status', 'alasan_penolakan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'atasan_approved_at' => 'datetime',
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

    public function atasanApprovedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'atasan_approved_by');
    }

    public function scopeMemotongKuota($query)
    {
        return $query->where(function ($q) {
            $q->whereNotIn('jenis_cuti', self::JENIS_BEBAS_KUOTA_JIKA_ADA_LAMPIRAN)
              ->orWhereNull('lampiran_path');
        });
    }
}