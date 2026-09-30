<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\CutiRequest;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CutiApprovalController extends Controller
{
    public function pengajuan()
    {
        $daftar = CutiRequest::where('status', 'menunggu_atasan')
            ->whereHas('user', fn ($q) => $q->where('atasan_id', Auth::id()))
            ->with('user')
            ->orderBy('tanggal_mulai')
            ->get();

        return view('atasan.cuti-anggota-divisi.pengajuan', ['daftar' => $daftar]);
    }

    public function approve(CutiRequest $cuti)
    {
        abort_unless($cuti->user->atasan_id === Auth::id(), 403);

        $cuti->update([
            'status' => 'disetujui',
            'atasan_approved_by' => Auth::id(),
            'atasan_approved_at' => now(),
            'disetujui_oleh' => Auth::id(),
        ]);

        $rentangTanggal = $cuti->tanggal_mulai->isSameDay($cuti->tanggal_selesai)
            ? $cuti->tanggal_mulai->translatedFormat('l, d F Y')
            : $cuti->tanggal_mulai->translatedFormat('l, d F Y').' s/d '.$cuti->tanggal_selesai->translatedFormat('l, d F Y');

        NotificationService::send(
            $cuti->user, 'cuti_disetujui',
            "Pengajuan {$cuti->jenis_cuti} Anda pada {$rentangTanggal} ({$cuti->jumlah_hari} hari) telah disetujui.",
            email: true, emailJudul: 'Pengajuan Cuti Disetujui'
        );

        NotificationService::broadcastPersetujuan(
            $cuti->user, Auth::user(), 'cuti_disetujui',
            "{$cuti->user->nama} disetujui cutinya ({$cuti->jenis_cuti}) pada {$rentangTanggal} ({$cuti->jumlah_hari} hari).",
            'Pemberitahuan Cuti Karyawan Disetujui'
        );

        return back()->with('status', 'Cuti berhasil disetujui.');
    }

    public function reject(Request $request, CutiRequest $cuti)
    {
        abort_unless($cuti->user->atasan_id === Auth::id(), 403);
        $request->validate(['alasan_penolakan' => 'nullable|string']);

        $cuti->update([
            'status' => 'ditolak',
            'atasan_approved_by' => Auth::id(),
            'atasan_approved_at' => now(),
            'alasan_penolakan' => $request->alasan_penolakan,
        ]);

        $rentangTanggal = $cuti->tanggal_mulai->isSameDay($cuti->tanggal_selesai)
            ? $cuti->tanggal_mulai->translatedFormat('l, d F Y')
            : $cuti->tanggal_mulai->translatedFormat('l, d F Y').' s/d '.$cuti->tanggal_selesai->translatedFormat('l, d F Y');

        NotificationService::send(
            $cuti->user, 'cuti_ditolak',
            "Pengajuan {$cuti->jenis_cuti} Anda pada {$rentangTanggal} ditolak oleh atasan.".($request->alasan_penolakan ? " Alasan: {$request->alasan_penolakan}" : ''),
            email: true, emailJudul: 'Pengajuan Cuti Ditolak'
        );

        return back()->with('status', 'Cuti berhasil ditolak.');
    }

    public function riwayat(Request $request)
    {
        $anggotaTim = User::where('atasan_id', Auth::id())->orderBy('nama')->get();

        $periode = $request->get('periode', now()->format('Y-m'));
        $bulan = \Carbon\Carbon::createFromFormat('Y-m', $periode);

        $data = $anggotaTim->map(function ($anggota) use ($bulan) {
            $tahun = $bulan->year;

            $terpakai = CutiRequest::where('user_id', $anggota->id)
                ->where('status', 'disetujui')
                ->whereYear('tanggal_mulai', $tahun)
                ->memotongKuota()
                ->sum('jumlah_hari');

            $riwayatBulanIni = CutiRequest::where('user_id', $anggota->id)
                ->whereMonth('tanggal_mulai', $bulan->month)
                ->whereYear('tanggal_mulai', $bulan->year)
                ->orderBy('tanggal_mulai')
                ->get();

            return (object) [
                'user' => $anggota,
                'jatah' => $anggota->jatah_cuti_tahunan,
                'terpakai' => $terpakai,
                'sisa' => $anggota->jatah_cuti_tahunan - $terpakai,
                'riwayat' => $riwayatBulanIni,
            ];
        });

        return view('atasan.cuti-anggota-divisi.riwayat', [
            'data' => $data,
            'periode' => $periode,
        ]);
    }

    public function riwayatDetail(Request $request, User $user)
    {
        abort_unless($user->atasan_id === Auth::id(), 403);

        $periode = $request->get('periode', now()->format('Y-m'));
        $bulan = \Carbon\Carbon::createFromFormat('Y-m', $periode);

        $terpakai = CutiRequest::where('user_id', $user->id)
            ->where('status', 'disetujui')
            ->whereYear('tanggal_mulai', $bulan->year)
            ->memotongKuota()
            ->sum('jumlah_hari');

        $riwayat = CutiRequest::where('user_id', $user->id)
            ->whereMonth('tanggal_mulai', $bulan->month)
            ->whereYear('tanggal_mulai', $bulan->year)
            ->orderBy('tanggal_mulai')
            ->get();

        return view('atasan.cuti-anggota-divisi.riwayat-detail', [
            'anggota' => $user,
            'jatah' => $user->jatah_cuti_tahunan,
            'terpakai' => $terpakai,
            'sisa' => $user->jatah_cuti_tahunan - $terpakai,
            'riwayat' => $riwayat,
            'periode' => $periode,
        ]);
    }
}