<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\CutiRequest;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CutiController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $tahun = now()->year;

        $terpakai = CutiRequest::where('user_id', $user->id)->where('status', 'disetujui')->whereYear('tanggal_mulai', $tahun)->sum('jumlah_hari');
        $terakhir = CutiRequest::where('user_id', $user->id)->orderByDesc('created_at')->limit(5)->get();

        return view('atasan.cuti.dashboard', [
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

        $terpakai = CutiRequest::where('user_id', $user->id)->where('status', 'disetujui')->whereYear('tanggal_mulai', $tahun)->sum('jumlah_hari');
        $menunggu = CutiRequest::where('user_id', $user->id)->where('status', 'menunggu_hrd')->count();

        return view('atasan.cuti.ajukan', [
            'sisaKuota' => $user->jatah_cuti_tahunan - $terpakai,
            'jatah' => $user->jatah_cuti_tahunan,
            'terpakai' => $terpakai,
            'menunggu' => $menunggu,
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
        $lampiranPath = $request->hasFile('lampiran') ? $request->file('lampiran')->store('cuti-lampiran', 'public') : null;

        CutiRequest::create([
            'user_id' => $user->id,
            'jenis_cuti' => $data['jenis_cuti'],
            'tanggal_mulai' => $mulai,
            'tanggal_selesai' => $selesai,
            'jumlah_hari' => $jumlahHari,
            'keterangan' => $data['keterangan'] ?? null,
            'lampiran_path' => $lampiranPath,
            'status' => 'menunggu_hrd',
        ]);

        User::whereHas('role', fn ($q) => $q->where('nama', 'HRD'))->get()->each(function ($hrd) use ($user, $data, $jumlahHari) {
            NotificationService::send(
                $hrd, 'cuti_menunggu',
                "{$user->nama} (Atasan) mengajukan {$data['jenis_cuti']} ({$jumlahHari} hari), menunggu persetujuan Anda.",
                email: true, emailJudul: 'Pengajuan Cuti Menunggu Persetujuan'
            );
        });

        return redirect()->route('atasan.cuti.dashboard')->with('status', 'Pengajuan cuti berhasil dikirim.');
    }

    public function riwayat(Request $request)
    {
        $query = CutiRequest::where('user_id', Auth::id());
        if ($request->filled('tahun')) $query->whereYear('tanggal_mulai', $request->tahun);
        if ($request->filled('status')) $query->where('status', $request->status);

        return view('atasan.cuti.riwayat', ['riwayat' => $query->orderByDesc('tanggal_mulai')->get()]);
    }

    public function notifikasi()
    {
        $notifications = \App\Models\NotificationWpm::where('user_id', Auth::id())
            ->whereIn('type', ['cuti_menunggu', 'cuti_disetujui', 'cuti_ditolak'])
            ->orderByDesc('sent_at')->get();

        return view('atasan.cuti.notifikasi', ['notifications' => $notifications]);
    }
}