<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['target_bulanan_id', 'minggu_ke', 'nilai_target'])]
class TargetMingguan extends Model
{
    use HasUuids;

    protected $table = 'target_mingguan';

    protected function casts(): array
    {
        return ['nilai_target' => 'decimal:2'];
    }

    public function targetBulanan(): BelongsTo
    {
        return $this->belongsTo(TargetBulanan::class);
    }

    public function weeklyCommitment(): HasOne
    {
        return $this->hasOne(WeeklyCommitment::class);
    }

    public function actualMingguan(): HasOne
    {
        return $this->hasOne(ActualMingguan::class);
    }
}