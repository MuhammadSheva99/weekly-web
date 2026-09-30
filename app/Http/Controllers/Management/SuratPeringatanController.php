<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\SuratPeringatan;
use Illuminate\Http\Request;

class SuratPeringatanController extends Controller
{
    public function index(Request $request)
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

        return view('management.sp-karyawan.index', [
            'daftar' => $query->orderByDesc('tanggal_terbit')->get(),
            'divisiList' => Divisi::orderBy('nama')->get(),
        ]);
    }
}