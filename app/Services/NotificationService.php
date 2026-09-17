<?php

namespace App\Services;

use App\Mail\NotificationMail;
use App\Models\NotificationWpm;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public static function send(User $user, string $type, string $message, bool $email = false, ?string $emailJudul = null): void
    {
        NotificationWpm::create([
            'user_id' => $user->id,
            'type' => $type,
            'message' => $message,
            'is_read' => false,
            'sent_at' => now(),
        ]);

        if ($email) {
            Mail::to($user->email)->send(new NotificationMail($emailJudul ?? $message, $message));
        }
    }
}