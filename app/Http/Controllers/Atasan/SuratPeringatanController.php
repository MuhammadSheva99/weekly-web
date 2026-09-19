<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\CompanyDocument;
use App\Models\SuratPeringatan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SuratPeringatanController extends Controller
{
    public function peraturan()
    {
        $dokumen = CompanyDocument::latest()->first();

        return view('atasan.surat-peringatan.peraturan', ['dokumen' => $dokumen]);
    }

    public function riwayat()
    {
        $anggotaTimIds = User::where('atasan_id', Auth::id())->pluck('id');

        $daftar = SuratPeringatan::whereIn('user_id', $anggotaTimIds)
            ->with('user')
            ->orderByDesc('tanggal_terbit')
            ->get();

        return view('atasan.surat-peringatan.riwayat', ['daftar' => $daftar]);
    }
}