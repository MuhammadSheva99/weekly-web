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
            'status' => 'menunggu_hrd',
            'atasan_approved_by' => Auth::id(),
            'atasan_approved_at' => now(),
        ]);

        User::whereHas('role', fn ($q) => $q->where('nama', 'HRD'))->get()->each(function ($hrd) use ($cuti) {
            NotificationService::send(
                $hrd, 'cuti_menunggu',
                "{$cuti->user->nama} mengajukan {$cuti->jenis_cuti} ({$cuti->jumlah_hari} hari), sudah disetujui atasan, menunggu persetujuan Anda.",
                email: true, emailJudul: 'Pengajuan Cuti Menunggu Persetujuan HRD'
            );
        });

        return back()->with('status', 'Pengajuan diteruskan ke HRD.');
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

        NotificationService::send(
            $cuti->user, 'cuti_ditolak',
            "Pengajuan {$cuti->jenis_cuti} Anda ditolak oleh atasan.".($request->alasan_penolakan ? " Alasan: {$request->alasan_penolakan}" : ''),
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