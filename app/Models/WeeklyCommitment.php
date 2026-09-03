<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'target_mingguan_id', 'user_id', 'big_goal', 'prioritas',
    'metric', 'target', 'output_deliverable', 'status', 'submitted_at',
])]
class WeeklyCommitment extends Model
{
    use HasUuids;

    protected $table = 'weekly_commitment';

    protected function casts(): array
    {
        return [
            'prioritas' => 'array',
            'metric' => 'array',
            'target' => 'decimal:2',
            'submitted_at' => 'datetime',
        ];
    }

    public function targetMingguan(): BelongsTo
    {
        return $this->belongsTo(TargetMingguan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function weeklyProgress(): HasOne
    {
        return $this->hasOne(WeeklyProgress::class);
    }

    public function selfReview(): HasOne
    {
        return $this->hasOne(SelfReview::class);
    }

    public function actualMingguan(): HasOne
    {
        return $this->hasOne(ActualMingguan::class);
    }

    public function approvalComments(): HasMany
    {
        return $this->hasMany(ApprovalComment::class);
    }
}