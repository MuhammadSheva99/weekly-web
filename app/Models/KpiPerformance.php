<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['kpi_id', 'user_id', 'periode', 'actual_bulanan', 'achievement_pct', 'score'])]
class KpiPerformance extends Model
{
    use HasUuids;

    protected $table = 'kpi_performance';

    protected function casts(): array
    {
        return [
            'periode' => 'date',
            'actual_bulanan' => 'decimal:2',
            'achievement_pct' => 'decimal:2',
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
}