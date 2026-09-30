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

    /**
     * Kirim notifikasi ke banyak user sekaligus.
     * Notifikasi in-app tetap dibuat 1 baris per user.
     * Email digabung jadi 1 pengiriman (Bcc semua penerima), bukan dikirim satu-satu.
     */
    public static function sendBulk(iterable $users, string $type, string $message, bool $email = false, ?string $emailJudul = null): void
    {
        $users = collect($users)->filter()->unique('id')->values();

        if ($users->isEmpty()) {
            return;
        }

        foreach ($users as $user) {
            NotificationWpm::create([
                'user_id' => $user->id,
                'type' => $type,
                'message' => $message,
                'is_read' => false,
                'sent_at' => now(),
            ]);
        }

        if ($email) {
            $emails = $users->pluck('email')->filter()->unique()->values();

            if ($emails->isNotEmpty()) {
                Mail::to(config('mail.from.address'))
                    ->bcc($emails->all())
                    ->send(new NotificationMail($emailJudul ?? $message, $message));
            }
        }
    }
    
    public static function broadcastPersetujuan(User $requester, User $approver, string $type, string $message, string $emailJudul): void
    {
        $penerima = User::where('id', '!=', $requester->id)
            ->where('id', '!=', $approver->id)
            ->where(function ($q) use ($requester) {
                $q->whereHas('role', fn ($r) => $r->whereIn('nama', ['Atasan', 'HRD', 'Management']))
                  ->orWhere('divisi_id', $requester->divisi_id);
            })
            ->get();

        self::sendBulk($penerima, $type, $message, email: true, emailJudul: $emailJudul);
    }
}