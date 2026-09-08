<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['nama', 'email', 'password', 'role_id', 'divisi_id', 'atasan_id', 'jabatan', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, HasUuids, Notifiable;

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class);
    }

    /** Atasan langsung dari user ini (self-reference) */
    public function atasan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'atasan_id');
    }

    /** Daftar bawahan langsung dari user ini */
    public function bawahan(): HasMany
    {
        return $this->hasMany(User::class, 'atasan_id');
    }

    public function targetBulanan(): HasMany
    {
        return $this->hasMany(TargetBulanan::class);
    }

    public function weeklyCommitment(): HasMany
    {
        return $this->hasMany(WeeklyCommitment::class);
    }

    public function kpiPerformance(): HasMany
    {
        return $this->hasMany(KpiPerformance::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(NotificationWpm::class);
    }

    public function approvalComments(): HasMany
    {
        return $this->hasMany(ApprovalComment::class, 'commented_by');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'changed_by');
    }
    
    public function cutiRequests(): HasMany
    {
        return $this->hasMany(CutiRequest::class);
    }
}