<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['nama', 'divisi_id', 'email'])]
class Karyawan extends Model
{
    use HasUuids;

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class);
    }
}