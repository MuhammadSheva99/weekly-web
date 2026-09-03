<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'weekly_commitment_id', 'target_mingguan_id',
    'nilai_actual_final', 'achievement_pct', 'locked_at',
])]
class ActualMingguan extends Model
{
    use HasUuids;

    protected $table = 'actual_mingguan';

    protected function casts(): array
    {
        return [
            'nilai_actual_final' => 'decimal:2',
            'achievement_pct' => 'decimal:2',
            'locked_at' => 'datetime',
        ];
    }

    public function weeklyCommitment(): BelongsTo
    {
        return $this->belongsTo(WeeklyCommitment::class);
    }

    public function targetMingguan(): BelongsTo
    {
        return $this->belongsTo(TargetMingguan::class);
    }
}