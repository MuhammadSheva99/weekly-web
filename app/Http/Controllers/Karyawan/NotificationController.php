<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\NotificationWpm;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = NotificationWpm::where('user_id', Auth::id())
            ->orderByDesc('sent_at')
            ->get();

        return view('karyawan.notifications', ['notifications' => $notifications]);
    }
}