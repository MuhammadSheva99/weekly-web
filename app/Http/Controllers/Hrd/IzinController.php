<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\IzinRequest;
use App\Models\NotificationWpm;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IzinController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        $izinBulanIni = IzinRequest::where('user_id', $user->id)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->with(['disetujuiOleh', 'atasanApprovedBy'])
            ->orderBy('tanggal')
            ->get();

        return view('hrd.izin.dashboard', [
            'jumlahIzinBulanIni' => $izinBulanIni->count(),
            'izinBulanIni' => $izinBulanIni,
        ]);
    }

    public function createForm()
    {
        return view('hrd.izin.ajukan', [
            'jenisIzin' => IzinRequest::JENIS_IZIN,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'jenis_izin' => 'required|in:'.implode(',', array_keys(IzinRequest::JENIS_IZIN)),
            'tanggal' => 'required|date',
            'jam_mulai' => 'required',
            'estimasi_kembali' => 'nullable',
            'keterangan' => 'required|string',
            'lampiran' => 'nullable|file|max:5120',
        ]);

        $user = Auth::user();
        $lampiranPath = $request->hasFile('lampiran')
            ? $request->file('lampiran')->store('izin-lampiran', 'public')
            : null;

        IzinRequest::create([
            'user_id' => $user->id,
            'jenis_izin' => $data['jenis_izin'],
            'tanggal' => $data['tanggal'],
            'jam_mulai' => $data['jam_mulai'],
            'estimasi_kembali' => $data['estimasi_kembali'] ?? null,
            'keterangan' => $data['keterangan'],
            'lampiran_path' => $lampiranPath,
            'status' => 'disetujui',
            'disetujui_oleh' => $user->id,
        ]);

        NotificationService::send(
            $user, 'izin_disetujui',
            "Pengajuan izin Anda ({$data['jenis_izin']}) otomatis disetujui.",
        );

        return redirect()->route('hrd.izin.dashboard')->with('status', 'Pengajuan izin berhasil dikirim.');
    }

    public function riwayat(Request $request)
    {
        $query = IzinRequest::where('user_id', Auth::id());

        if ($request->filled('periode')) {
            $bulan = \Carbon\Carbon::createFromFormat('Y-m', $request->periode);
            $query->whereMonth('tanggal', $bulan->month)->whereYear('tanggal', $bulan->year);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return view('hrd.izin.riwayat', [
            'riwayat' => $query->with(['disetujuiOleh', 'atasanApprovedBy'])->orderByDesc('tanggal')->get(),
            'periode' => $request->get('periode', now()->format('Y-m')),
        ]);
    }

    public function notifikasi()
    {
        $notifications = NotificationWpm::where('user_id', Auth::id())
            ->whereIn('type', ['izin_menunggu', 'izin_disetujui', 'izin_ditolak'])
            ->orderByDesc('sent_at')
            ->get();

        return view('hrd.izin.notifikasi', ['notifications' => $notifications]);
    }
}