<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\CompanyDocument;
use App\Models\SpRekomendasi;
use App\Models\SuratPeringatan;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
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

    public function riwayatSaya()
    {
        $daftar = SuratPeringatan::where('user_id', Auth::id())
            ->orderByDesc('tanggal_terbit')
            ->get();

        return view('atasan.surat-peringatan.riwayat-saya', ['daftar' => $daftar]);
    }

    public function rekomendasiForm()
    {
        $anggotaTim = User::where('atasan_id', Auth::id())->orderBy('nama')->get();

        return view('atasan.surat-peringatan.rekomendasi', ['anggotaTim' => $anggotaTim]);
    }

    public function rekomendasiStore(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'level_usulan' => 'required|in:SP1,SP2,SP3',
            'alasan' => 'required|string',
            'lampiran' => 'nullable|file|max:5120',
        ]);

        $anggota = User::findOrFail($data['user_id']);
        abort_unless($anggota->atasan_id === Auth::id(), 403);

        $lampiranPath = $request->hasFile('lampiran')
            ? $request->file('lampiran')->store('sp-rekomendasi-lampiran', 'public')
            : null;

        SpRekomendasi::create([
            'user_id' => $data['user_id'],
            'direkomendasikan_oleh' => Auth::id(),
            'level_usulan' => $data['level_usulan'],
            'alasan' => $data['alasan'],
            'lampiran_path' => $lampiranPath,
            'status' => 'menunggu',
        ]);

        User::whereHas('role', fn ($q) => $q->where('nama', 'HRD'))->get()->each(function ($hrd) use ($anggota, $data) {
            NotificationService::send(
                $hrd, 'sp_rekomendasi_baru',
                "Atasan merekomendasikan SP ({$data['level_usulan']}) untuk {$anggota->nama}.",
                email: true, emailJudul: 'Rekomendasi SP Baru dari Atasan'
            );
        });

        return redirect()->route('atasan.surat-peringatan.rekomendasi.riwayat')->with('status', 'Rekomendasi SP berhasil dikirim ke HRD.');
    }

    public function rekomendasiRiwayat()
    {
        $daftar = SpRekomendasi::where('direkomendasikan_oleh', Auth::id())
            ->with(['user', 'suratPeringatan'])
            ->orderByDesc('created_at')
            ->get();

        return view('atasan.surat-peringatan.rekomendasi-riwayat', ['daftar' => $daftar]);
    }
}