<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\CompanyDocument;
use App\Models\SuratPeringatan;
use Illuminate\Support\Facades\Auth;

class SuratPeringatanController extends Controller
{
    public function peraturan()
    {
        $dokumen = CompanyDocument::latest()->first();

        return view('karyawan.surat-peringatan.peraturan', ['dokumen' => $dokumen]);
    }

    public function riwayat()
    {
        $daftar = SuratPeringatan::where('user_id', Auth::id())
            ->orderByDesc('tanggal_terbit')
            ->get();

        return view('karyawan.surat-peringatan.riwayat', ['daftar' => $daftar]);
    }
}