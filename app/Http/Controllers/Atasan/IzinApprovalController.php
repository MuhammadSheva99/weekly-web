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
            'status' => 'menunggu_hrd',
            'atasan_approved_by' => Auth::id(),
            'atasan_approved_at' => now(),
        ]);

        $label = $izin->label_jenis_izin;

        User::whereHas('role', fn ($q) => $q->where('nama', 'HRD'))->get()->each(function ($hrd) use ($izin, $label) {
            NotificationService::send(
                $hrd, 'izin_menunggu',
                "{$izin->user->nama} mengajukan {$label}, sudah disetujui atasan, menunggu persetujuan Anda.",
                email: true, emailJudul: 'Pengajuan Izin Menunggu Persetujuan HRD'
            );
        });

        return back()->with('status', 'Pengajuan diteruskan ke HRD.');
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

        NotificationService::send(
            $izin->user, 'izin_ditolak',
            "Pengajuan {$izin->label_jenis_izin} Anda ditolak oleh atasan.".($request->alasan_penolakan ? " Alasan: {$request->alasan_penolakan}" : ''),
            email: true, emailJudul: 'Pengajuan Izin Ditolak'
        );

        return back()->with('status', 'Izin berhasil ditolak.');
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
}