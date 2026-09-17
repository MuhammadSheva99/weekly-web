<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IzinRequest extends Model
{
    use HasUuids;

    const JENIS_IZIN = [
        'berangkat_siang' => 'Izin berangkat siang',
        'pulang_cepat' => 'Izin pulang cepat',
        'berangkat_terlambat' => 'Izin berangkat terlambat',
        'keluar_sementara' => 'Izin keluar sementara',
    ];

    protected $fillable = [
        'user_id', 'jenis_izin', 'tanggal', 'jam_mulai', 'estimasi_kembali',
        'keterangan', 'lampiran_path', 'atasan_approved_by', 'atasan_approved_at',
        'disetujui_oleh', 'status', 'alasan_penolakan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'atasan_approved_at' => 'datetime',
        ];
    }

    public function getLabelJenisIzinAttribute(): string
    {
        return self::JENIS_IZIN[$this->jenis_izin] ?? $this->jenis_izin;
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
}