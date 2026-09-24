<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\IzinRequest;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IzinKaryawanController extends Controller
{
    public function pengajuan()
    {
        $daftar = IzinRequest::where('status', 'menunggu_hrd')
            ->with(['user.divisi', 'user.atasan'])
            ->orderBy('tanggal')
            ->get();

        return view('hrd.izin-karyawan.pengajuan', ['daftar' => $daftar]);
    }

    public function approve(IzinRequest $izin)
    {
        $izin->update([
            'status' => 'disetujui',
            'disetujui_oleh' => Auth::id(),
        ]);

        NotificationService::send(
            $izin->user, 'izin_disetujui',
            "Pengajuan {$izin->label_jenis_izin} Anda telah disetujui.",
            email: true, emailJudul: 'Pengajuan Izin Disetujui'
        );

        return back()->with('status', 'Izin berhasil disetujui.');
    }

    public function reject(Request $request, IzinRequest $izin)
    {
        $request->validate(['alasan_penolakan' => 'nullable|string']);

        $izin->update([
            'status' => 'ditolak',
            'disetujui_oleh' => Auth::id(),
            'alasan_penolakan' => $request->alasan_penolakan,
        ]);

        NotificationService::send(
            $izin->user, 'izin_ditolak',
            "Pengajuan {$izin->label_jenis_izin} Anda ditolak.".($request->alasan_penolakan ? " Alasan: {$request->alasan_penolakan}" : ''),
            email: true, emailJudul: 'Pengajuan Izin Ditolak'
        );

        return back()->with('status', 'Izin berhasil ditolak.');
    }

    public function riwayat(Request $request)
    {
        $query = User::query();

        if ($request->filled('divisi_id')) {
            $query->where('divisi_id', $request->divisi_id);
        }

        $karyawan = $query->orderBy('nama')->get();

        $periode = $request->get('periode', now()->format('Y-m'));
        $bulan = \Carbon\Carbon::createFromFormat('Y-m', $periode);

        $data = $karyawan->map(function ($k) use ($bulan) {
            $riwayatBulanIni = IzinRequest::where('user_id', $k->id)
                ->whereMonth('tanggal', $bulan->month)
                ->whereYear('tanggal', $bulan->year)
                ->orderBy('tanggal')
                ->get();

            return (object) [
                'user' => $k,
                'jumlah' => $riwayatBulanIni->count(),
                'riwayat' => $riwayatBulanIni,
            ];
        });

        return view('hrd.izin-karyawan.riwayat', [
            'data' => $data,
            'periode' => $periode,
            'divisiList' => Divisi::orderBy('nama')->get(),
        ]);
    }

    public function riwayatDetail(Request $request, User $karyawan)
    {
        $periode = $request->get('periode', now()->format('Y-m'));
        $bulan = \Carbon\Carbon::createFromFormat('Y-m', $periode);

        $riwayat = IzinRequest::where('user_id', $karyawan->id)
            ->whereMonth('tanggal', $bulan->month)
            ->whereYear('tanggal', $bulan->year)
            ->orderBy('tanggal')
            ->get();

        return view('hrd.izin-karyawan.riwayat.detail', [
            'karyawan' => $karyawan,
            'riwayat' => $riwayat,
            'periode' => $periode,
        ]);
    }
}