<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\SuratPeringatan;
use App\Models\SpRekomendasi;
use App\Models\User;
use Illuminate\Http\Request;

class SuratPeringatanController extends Controller
{
    // 1. Tab Terbitkan SP (Form)
    public function terbitkanForm()
    {
        $karyawans = User::whereHas('role', function ($q) {
            $q->whereIn('nama', ['Karyawan', 'Atasan']);
        })->orderBy('nama')->get();

        return view('management.sp-karyawan.terbitkan', compact('karyawans'));
    }

    // Process Terbitkan SP
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tingkat' => 'required|in:1,2,3',
            'tanggal_terbit' => 'required|date',
            'alasan' => 'required|string',
            'konsekuensi' => 'nullable|string',
        ]);

        $tanggalTerbit = \Carbon\Carbon::parse($request->tanggal_terbit);
        $tanggalBerakhir = $tanggalTerbit->copy()->addMonths(3);

        // Generate No. SP (misal: SP-2026-001)
        $countToday = SuratPeringatan::whereYear('created_at', now()->year)->count() + 1;
        $noSp = 'SP-' . now()->year . '-' . str_pad($countToday, 3, '0', STR_PAD_LEFT);

        SuratPeringatan::create([
            'no_sp' => $noSp,
            'user_id' => $request->user_id,
            'diterbitkan_oleh' => auth()->id(),
            'tingkat' => $request->tingkat,
            'tanggal_terbit' => $tanggalTerbit,
            'tanggal_berakhir' => $tanggalBerakhir,
            'alasan' => $request->alasan,
            'konsekuensi' => $request->konsekuensi,
        ]);

        return redirect()->route('management.sp-karyawan.riwayat')->with('success', 'Surat Peringatan berhasil diterbitkan.');
    }

    // 2. Tab Riwayat Pelanggaran
    public function riwayat(Request $request)
    {
        $query = SuratPeringatan::with(['user.divisi', 'diterbitkanOleh']);

        if ($request->filled('divisi_id')) {
            $query->whereHas('user', fn ($q) => $q->where('divisi_id', $request->divisi_id));
        }

        if ($request->filled('status')) {
            if ($request->status === 'aktif') {
                $query->where('tanggal_berakhir', '>=', now());
            } else {
                $query->where('tanggal_berakhir', '<', now());
            }
        }

        $daftar = $query->orderByDesc('tanggal_terbit')->get();
        $divisiList = Divisi::orderBy('nama')->get();

        return view('management.sp-karyawan.riwayat', compact('daftar', 'divisiList'));
    }

    // 3. Tab Rekomendasi dari Atasan
    public function rekomendasi()
    {
        $rekomendasiList = SpRekomendasi::with(['user.divisi', 'pengaju'])
            ->latest()
            ->get();

        return view('management.sp-karyawan.rekomendasi', compact('rekomendasiList'));
    }
}