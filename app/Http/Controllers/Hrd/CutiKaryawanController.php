<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\CutiRequest;
use App\Models\Divisi;
use App\Models\NotificationWpm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CutiKaryawanController extends Controller
{
    public function pengajuan()
    {
        $daftar = CutiRequest::where('status', 'menunggu')
            ->with(['user.divisi', 'user.atasan'])
            ->orderBy('tanggal_mulai')
            ->get();

        return view('hrd.cuti-karyawan.pengajuan', ['daftar' => $daftar]);
    }

    public function approve(CutiRequest $cuti)
    {
        $cuti->update([
            'status' => 'disetujui',
            'disetujui_oleh' => Auth::id(),
        ]);

        NotificationWpm::create([
            'user_id' => $cuti->user_id,
            'type' => 'cuti_disetujui',
            'message' => "Pengajuan {$cuti->jenis_cuti} Anda ({$cuti->jumlah_hari} hari) telah disetujui.",
            'is_read' => false,
            'sent_at' => now(),
        ]);

        return back()->with('status', 'Cuti berhasil disetujui.');
    }

    public function reject(Request $request, CutiRequest $cuti)
    {
        $request->validate(['alasan_penolakan' => 'nullable|string']);

        $cuti->update([
            'status' => 'ditolak',
            'disetujui_oleh' => Auth::id(),
            'alasan_penolakan' => $request->alasan_penolakan,
        ]);

        NotificationWpm::create([
            'user_id' => $cuti->user_id,
            'type' => 'cuti_ditolak',
            'message' => "Pengajuan {$cuti->jenis_cuti} Anda ditolak.".($request->alasan_penolakan ? " Alasan: {$request->alasan_penolakan}" : ''),
            'is_read' => false,
            'sent_at' => now(),
        ]);

        return back()->with('status', 'Cuti berhasil ditolak.');
    }

    public function riwayat(Request $request)
    {
        $query = CutiRequest::whereIn('status', ['disetujui', 'ditolak'])
            ->with(['user.divisi']);

        if ($request->filled('divisi_id')) {
            $query->whereHas('user', fn ($q) => $q->where('divisi_id', $request->divisi_id));
        }

        $riwayat = $query->orderByDesc('tanggal_mulai')->get()->map(function ($c) {
            $terpakaiTahunIni = CutiRequest::where('user_id', $c->user_id)
                ->where('status', 'disetujui')
                ->whereYear('tanggal_mulai', now()->year)
                ->sum('jumlah_hari');

            $c->sisa_cuti = $c->user->jatah_cuti_tahunan - $terpakaiTahunIni;
            return $c;
        });

        return view('hrd.cuti-karyawan.riwayat', [
            'riwayat' => $riwayat,
            'divisiList' => Divisi::orderBy('nama')->get(),
        ]);
    }
}