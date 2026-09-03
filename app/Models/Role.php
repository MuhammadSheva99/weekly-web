<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasUuids;

    protected $table = 'role';
    protected $fillable = ['nama'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}