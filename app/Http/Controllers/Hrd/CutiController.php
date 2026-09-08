<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\CutiRequest;
use App\Models\NotificationWpm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CutiController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $tahun = now()->year;

        $terpakai = CutiRequest::where('user_id', $user->id)
            ->where('status', 'disetujui')
            ->whereYear('tanggal_mulai', $tahun)
            ->sum('jumlah_hari');

        $terakhir = CutiRequest::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('hrd.cuti.dashboard', [
            'jatah' => $user->jatah_cuti_tahunan,
            'terpakai' => $terpakai,
            'sisaTahunLalu' => $user->sisa_cuti_tahun_lalu,
            'terakhir' => $terakhir,
        ]);
    }

    public function createForm()
    {
        $user = Auth::user();
        $tahun = now()->year;

        $terpakai = CutiRequest::where('user_id', $user->id)
            ->where('status', 'disetujui')
            ->whereYear('tanggal_mulai', $tahun)
            ->sum('jumlah_hari');

        $menunggu = CutiRequest::where('user_id', $user->id)->where('status', 'menunggu')->count();

        return view('hrd.cuti.ajukan', [
            'sisaKuota' => $user->jatah_cuti_tahunan - $terpakai,
            'jatah' => $user->jatah_cuti_tahunan,
            'terpakai' => $terpakai,
            'menunggu' => $menunggu,
            'atasan' => $user->atasan,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'jenis_cuti' => 'required|string|max:50',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'nullable|string',
            'lampiran' => 'nullable|file|max:5120',
        ]);

        $mulai = \Carbon\Carbon::parse($data['tanggal_mulai']);
        $selesai = \Carbon\Carbon::parse($data['tanggal_selesai']);
        $jumlahHari = $mulai->diffInDays($selesai) + 1;

        $user = Auth::user();
        $isHrd = $user->role->nama === 'HRD';

        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $lampiranPath = $request->file('lampiran')->store('cuti-lampiran', 'public');
        }

        $cuti = CutiRequest::create([
            'user_id' => $user->id,
            'jenis_cuti' => $data['jenis_cuti'],
            'tanggal_mulai' => $mulai,
            'tanggal_selesai' => $selesai,
            'jumlah_hari' => $jumlahHari,
            'keterangan' => $data['keterangan'] ?? null,
            'lampiran_path' => $lampiranPath,
            'status' => $isHrd ? 'disetujui' : 'menunggu',
            'disetujui_oleh' => $isHrd ? $user->id : null,
        ]);

        // Notifikasi
        if ($isHrd) {
            NotificationWpm::create([
                'user_id' => $user->id,
                'type' => 'cuti_disetujui',
                'message' => "Pengajuan cuti Anda ({$data['jenis_cuti']}, {$jumlahHari} hari) otomatis disetujui.",
                'is_read' => false,
                'sent_at' => now(),
            ]);
        } elseif ($user->atasan) {
            NotificationWpm::create([
                'user_id' => $user->atasan->id,
                'type' => 'cuti_menunggu',
                'message' => "{$user->nama} mengajukan {$data['jenis_cuti']} ({$jumlahHari} hari), menunggu persetujuan Anda.",
                'is_read' => false,
                'sent_at' => now(),
            ]);
        }

        return redirect()->route('hrd.cuti.dashboard')->with('status', 'Pengajuan cuti berhasil dikirim.');
    }

    public function riwayat(Request $request)
    {
        $query = CutiRequest::where('user_id', Auth::id());

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_mulai', $request->tahun);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return view('hrd.cuti.riwayat', [
            'riwayat' => $query->orderByDesc('tanggal_mulai')->get(),
        ]);
    }

    public function notifikasi()
    {
        $notifications = NotificationWpm::where('user_id', Auth::id())
            ->whereIn('type', ['cuti_menunggu', 'cuti_disetujui', 'cuti_ditolak'])
            ->orderByDesc('sent_at')
            ->get();

        return view('hrd.cuti.notifikasi', ['notifications' => $notifications]);
    }
}