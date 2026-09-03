<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['table_name', 'record_id', 'action', 'old_value', 'new_value', 'changed_by', 'changed_at'])]
class AuditLog extends Model
{
    use HasUuids;

    protected $table = 'audit_log';

    // Tabel ini tidak punya created_at/updated_at sama sekali
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'old_value' => 'array',
            'new_value' => 'array',
            'changed_at' => 'datetime',
        ];
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}