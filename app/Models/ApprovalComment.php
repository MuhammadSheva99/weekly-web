<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['weekly_commitment_id', 'commented_by', 'comment'])]
class ApprovalComment extends Model
{
    use HasUuids;

    protected $table = 'approval_comment';

    // Tabel ini cuma punya kolom created_at, tidak ada updated_at
    const UPDATED_AT = null;

    public function weeklyCommitment(): BelongsTo
    {
        return $this->belongsTo(WeeklyCommitment::class);
    }

    public function commentedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'commented_by');
    }
}