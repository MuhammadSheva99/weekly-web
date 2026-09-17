<?php

namespace App\Http\Controllers\Karyawan;

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

        $terpakai = CutiRequest::where('user_id', $user->id)
            ->where('status', 'disetujui')
            ->whereYear('tanggal_mulai', $tahun)
            ->sum('jumlah_hari');

        $cutiBulanIni = CutiRequest::where('user_id', $user->id)
            ->whereMonth('tanggal_mulai', now()->month)
            ->whereYear('tanggal_mulai', now()->year)
            ->with('disetujuiOleh')
            ->orderBy('tanggal_mulai')
            ->get();

        return view('karyawan.cuti.dashboard', [
            'jatah' => $user->jatah_cuti_tahunan,
            'terpakai' => $terpakai,
            'cutiBulanIni' => $cutiBulanIni,
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

        $menunggu = CutiRequest::where('user_id', $user->id)
            ->whereIn('status', ['menunggu_atasan', 'menunggu_hrd'])
            ->count();

        return view('karyawan.cuti.ajukan', [
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
        $statusAwal = $user->atasan_id ? 'menunggu_atasan' : 'menunggu_hrd';

        $lampiranPath = $request->hasFile('lampiran')
            ? $request->file('lampiran')->store('cuti-lampiran', 'public')
            : null;

        CutiRequest::create([
            'user_id' => $user->id,
            'jenis_cuti' => $data['jenis_cuti'],
            'tanggal_mulai' => $mulai,
            'tanggal_selesai' => $selesai,
            'jumlah_hari' => $jumlahHari,
            'keterangan' => $data['keterangan'] ?? null,
            'lampiran_path' => $lampiranPath,
            'status' => $statusAwal,
        ]);

        if ($statusAwal === 'menunggu_atasan') {
            NotificationService::send(
                $user->atasan, 'cuti_menunggu',
                "{$user->nama} mengajukan {$data['jenis_cuti']} ({$jumlahHari} hari), menunggu persetujuan Anda.",
                email: true, emailJudul: 'Pengajuan Cuti Menunggu Persetujuan'
            );
        } else {
            User::whereHas('role', fn ($q) => $q->where('nama', 'HRD'))->get()->each(function ($hrd) use ($user, $data, $jumlahHari) {
                NotificationService::send(
                    $hrd, 'cuti_menunggu',
                    "{$user->nama} mengajukan {$data['jenis_cuti']} ({$jumlahHari} hari), menunggu persetujuan Anda.",
                    email: true, emailJudul: 'Pengajuan Cuti Menunggu Persetujuan'
                );
            });
        }

        return redirect()->route('karyawan.cuti.dashboard')->with('status', 'Pengajuan cuti berhasil dikirim.');
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

        return view('karyawan.cuti.riwayat', [
            'riwayat' => $query->orderByDesc('tanggal_mulai')->get(),
        ]);
    }

    public function notifikasi()
    {
        $notifications = \App\Models\NotificationWpm::where('user_id', Auth::id())
            ->whereIn('type', ['cuti_menunggu', 'cuti_disetujui', 'cuti_ditolak'])
            ->orderByDesc('sent_at')
            ->get();

        return view('karyawan.cuti.notifikasi', ['notifications' => $notifications]);
    }
}