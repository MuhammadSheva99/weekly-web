<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiChat extends Model
{
    use HasFactory; // HasUuids dihapus agar kompatibel dengan ID auto-increment DB

    protected $table = 'ai_chats';

    protected $fillable = [
        'user_id',
        'weekly_commitment_id',
        'title',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function weeklyCommitment()
    {
        return $this->belongsTo(WeeklyCommitment::class, 'weekly_commitment_id');
    }

    public function messages()
    {
        return $this->hasMany(AiChatMessage::class, 'ai_chat_id');
    }
}