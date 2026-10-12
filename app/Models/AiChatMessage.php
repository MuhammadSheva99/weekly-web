<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiChatMessage extends Model
{
    use HasFactory;

    protected $table = 'ai_chat_messages';

    protected $fillable = [
        'ai_chat_id',
        'sender',
        'message',
    ];

    public function chat()
    {
        return $this->belongsTo(AiChat::class, 'ai_chat_id');
    }
}