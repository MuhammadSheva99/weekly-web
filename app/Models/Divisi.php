<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Divisi extends Model
{
    use HasUuids;

    protected $table = 'divisi';
    protected $fillable = ['nama'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function kpiMaster(): HasMany
    {
        return $this->hasMany(KpiMaster::class);
    }
}