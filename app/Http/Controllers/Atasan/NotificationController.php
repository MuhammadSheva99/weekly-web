<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\NotificationWpm;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = NotificationWpm::where('user_id', Auth::id())
            ->whereIn('type', [
                'commitment_submitted', 'progress_submitted', 'review_submitted',
                'alert_underperform_anggota', 'alert_belum_submit_anggota',
                'cuti_menunggu', 'izin_menunggu',
            ])
            ->orderByDesc('sent_at')
            ->get();

        return view('atasan.notifications', ['notifications' => $notifications]);
    }
}