<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'weekly_commitment_id', 'target_minggu', 'actual', 'achievement_pct',
    'apa_berhasil', 'apa_gagal', 'kenapa_gagal', 'apa_beda',
    'improvement_depan', 'kritik_diri', 'submitted_at',
])]
class SelfReview extends Model
{
    use HasUuids;

    protected $table = 'self_review';

    protected function casts(): array
    {
        return [
            'target_minggu' => 'decimal:2',
            'actual' => 'decimal:2',
            'achievement_pct' => 'decimal:2',
            'submitted_at' => 'datetime',
        ];
    }

    public function weeklyCommitment(): BelongsTo
    {
        return $this->belongsTo(WeeklyCommitment::class);
    }
}