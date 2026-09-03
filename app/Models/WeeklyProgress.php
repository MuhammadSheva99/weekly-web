<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'weekly_commitment_id', 'actual_sementara', 'achievement_pct',
    'problem', 'analysis', 'solution', 'action_plan', 'submitted_at',
])]
class WeeklyProgress extends Model
{
    use HasUuids;

    protected $table = 'weekly_progress';

    protected function casts(): array
    {
        return [
            'actual_sementara' => 'decimal:2',
            'achievement_pct' => 'decimal:2',
            'submitted_at' => 'datetime',
        ];
    }

    public function weeklyCommitment(): BelongsTo
    {
        return $this->belongsTo(WeeklyCommitment::class);
    }
}