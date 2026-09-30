<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\IzinRequest;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IzinApprovalController extends Controller
{
    public function pengajuan()
    {
        $daftar = IzinRequest::where('status', 'menunggu_atasan')
            ->whereHas('user', fn ($q) => $q->where('atasan_id', Auth::id()))
            ->with('user')
            ->orderBy('tanggal')
            ->get();

        return view('atasan.izin-anggota-divisi.pengajuan', ['daftar' => $daftar]);
    }

    public function approve(IzinRequest $izin)
    {
        abort_unless($izin->user->atasan_id === Auth::id(), 403);

        $izin->update([
            'status' => 'disetujui',
            'atasan_approved_by' => Auth::id(),
            'atasan_approved_at' => now(),
            'disetujui_oleh' => Auth::id(),
        ]);

        $label = $izin->label_jenis_izin;
        $infoWaktu = $this->formatInfoWaktu($izin);

        NotificationService::send(
            $izin->user, 'izin_disetujui',
            "Pengajuan {$label} Anda pada {$infoWaktu} telah disetujui.",
            email: true, emailJudul: 'Pengajuan Izin Disetujui'
        );

        NotificationService::broadcastPersetujuan(
            $izin->user, Auth::user(), 'izin_disetujui',
            "{$izin->user->nama} disetujui izinnya ({$label}) pada {$infoWaktu}.",
            'Pemberitahuan Izin Karyawan Disetujui'
        );

        return back()->with('status', 'Izin berhasil disetujui.');
    }

    public function reject(Request $request, IzinRequest $izin)
    {
        abort_unless($izin->user->atasan_id === Auth::id(), 403);
        $request->validate(['alasan_penolakan' => 'nullable|string']);

        $izin->update([
            'status' => 'ditolak',
            'atasan_approved_by' => Auth::id(),
            'atasan_approved_at' => now(),
            'alasan_penolakan' => $request->alasan_penolakan,
        ]);

        $infoWaktu = $this->formatInfoWaktu($izin);

        NotificationService::send(
            $izin->user, 'izin_ditolak',
            "Pengajuan {$izin->label_jenis_izin} Anda pada {$infoWaktu} ditolak oleh atasan.".($request->alasan_penolakan ? " Alasan: {$request->alasan_penolakan}" : ''),
            email: true, emailJudul: 'Pengajuan Izin Ditolak'
        );

        return back()->with('status', 'Izin berhasil ditolak.');
    }

    private function formatInfoWaktu(IzinRequest $izin): string
    {
        $jamMulai = \Carbon\Carbon::parse($izin->jam_mulai)->format('H:i');
        $estimasi = $izin->estimasi_kembali ? \Carbon\Carbon::parse($izin->estimasi_kembali)->format('H:i') : null;

        return $izin->tanggal->translatedFormat('l, d F Y').', jam '.$jamMulai.($estimasi ? " s/d {$estimasi}" : '');
    }

    public function riwayat(Request $request)
    {
        $anggotaTim = User::where('atasan_id', Auth::id())->orderBy('nama')->get();

        $periode = $request->get('periode', now()->format('Y-m'));
        $bulan = \Carbon\Carbon::createFromFormat('Y-m', $periode);

        $data = $anggotaTim->map(function ($anggota) use ($bulan) {
            $riwayatBulanIni = IzinRequest::where('user_id', $anggota->id)
                ->whereMonth('tanggal', $bulan->month)
                ->whereYear('tanggal', $bulan->year)
                ->orderBy('tanggal')
                ->get();

            return (object) [
                'user' => $anggota,
                'jumlah' => $riwayatBulanIni->count(),
                'riwayat' => $riwayatBulanIni,
            ];
        });

        return view('atasan.izin-anggota-divisi.riwayat', [
            'data' => $data,
            'periode' => $periode,
        ]);
    }

    public function riwayatDetail(Request $request, User $user)
    {
        abort_unless($user->atasan_id === Auth::id(), 403);

        $periode = $request->get('periode', now()->format('Y-m'));
        $bulan = \Carbon\Carbon::createFromFormat('Y-m', $periode);

        $riwayat = IzinRequest::where('user_id', $user->id)
            ->whereMonth('tanggal', $bulan->month)
            ->whereYear('tanggal', $bulan->year)
            ->orderBy('tanggal')
            ->get();

        return view('atasan.izin-anggota-divisi.riwayat-detail', [
            'anggota' => $user,
            'riwayat' => $riwayat,
            'periode' => $periode,
        ]);
    }
}