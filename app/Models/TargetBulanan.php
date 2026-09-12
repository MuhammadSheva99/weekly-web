<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['kpi_id', 'user_id', 'periode', 'nilai_target', 'bobot'])]
class TargetBulanan extends Model
{
    use HasUuids;

    protected $table = 'target_bulanan';

    protected function casts(): array
    {
        return [
            'periode' => 'date',
            'nilai_target' => 'decimal:2',
            'bobot' => 'decimal:2',
        ];
    }

    public function kpi(): BelongsTo
    {
        return $this->belongsTo(KpiMaster::class, 'kpi_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function targetMingguan(): HasMany
    {
        return $this->hasMany(TargetMingguan::class);
    }
}