<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'nama_kpi', 'satuan', 'pola', 'divisi_id', 'is_active', 'target_fleksibel',
    'key_result_area', 'deskripsi', 'sumber_data', 'rumus_realisasi', 'rumus_target',
])]
class KpiMaster extends Model
{
    use HasUuids;

    protected $table = 'kpi_master';

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'target_fleksibel' => 'boolean',
        ];
    }

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class);
    }

    public function targetBulanan(): HasMany
    {
        return $this->hasMany(TargetBulanan::class, 'kpi_id');
    }

    public function kpiPerformance(): HasMany
    {
        return $this->hasMany(KpiPerformance::class, 'kpi_id');
    }
}