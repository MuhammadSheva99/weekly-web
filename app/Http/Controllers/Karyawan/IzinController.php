<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\IzinRequest;
use App\Models\NotificationWpm;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IzinController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        $izinBulanIni = IzinRequest::where('user_id', $user->id)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->with(['disetujuiOleh', 'atasanApprovedBy'])
            ->orderBy('tanggal')
            ->get();

        return view('karyawan.izin.dashboard', [
            'jumlahIzinBulanIni' => $izinBulanIni->count(),
            'izinBulanIni' => $izinBulanIni,
        ]);
    }

    public function createForm()
    {
        $user = Auth::user();

        return view('karyawan.izin.ajukan', [
            'jenisIzin' => IzinRequest::JENIS_IZIN,
            'atasan' => $user->atasan,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'jenis_izin' => 'required|in:'.implode(',', array_keys(IzinRequest::JENIS_IZIN)),
            'tanggal' => 'required|date',
            'jam_mulai' => 'required',
            'estimasi_kembali' => 'nullable',
            'keterangan' => 'required|string',
            'lampiran' => 'nullable|file|max:5120',
        ]);

        $user = Auth::user();
        $isTerlambat = $data['jenis_izin'] === 'berangkat_terlambat';

        // Izin "berangkat terlambat" langsung tercatat, tidak perlu approval
        $statusAwal = $isTerlambat
            ? 'disetujui'
            : ($user->atasan_id ? 'menunggu_atasan' : 'menunggu_hrd');

        $lampiranPath = $request->hasFile('lampiran')
            ? $request->file('lampiran')->store('izin-lampiran', 'public')
            : null;

        $izin = IzinRequest::create([
            'user_id' => $user->id,
            'jenis_izin' => $data['jenis_izin'],
            'tanggal' => $data['tanggal'],
            'jam_mulai' => $data['jam_mulai'],
            'estimasi_kembali' => $data['estimasi_kembali'] ?? null,
            'keterangan' => $data['keterangan'],
            'lampiran_path' => $lampiranPath,
            'status' => $statusAwal,
        ]);

        $label = IzinRequest::JENIS_IZIN[$data['jenis_izin']];

        if ($isTerlambat) {
            // Notifikasi informasi saja, bukan permintaan approval
            $jamMulai = \Carbon\Carbon::parse($data['jam_mulai'])->format('H:i');
            $pesan = "{$user->nama} melapor {$label} pada {$izin->tanggal->format('d/m/Y')} jam {$jamMulai}. Tidak perlu persetujuan.";

            if ($user->atasan) {
                NotificationService::send(
                    $user->atasan, 'izin_menunggu',
                    $pesan,
                    email: true, emailJudul: 'Info: Karyawan Berangkat Terlambat'
                );
            }

            User::whereHas('role', fn ($q) => $q->where('nama', 'HRD'))->get()->each(function ($hrd) use ($pesan) {
                NotificationService::send(
                    $hrd, 'izin_menunggu',
                    $pesan,
                    email: true, emailJudul: 'Info: Karyawan Berangkat Terlambat'
                );
            });
        } elseif ($statusAwal === 'menunggu_atasan') {
            NotificationService::send(
                $user->atasan, 'izin_menunggu',
                "{$user->nama} mengajukan {$label}, menunggu persetujuan Anda.",
                email: true, emailJudul: 'Pengajuan Izin Menunggu Persetujuan'
            );
        } else {
            User::whereHas('role', fn ($q) => $q->where('nama', 'HRD'))->get()->each(function ($hrd) use ($user, $label) {
                NotificationService::send(
                    $hrd, 'izin_menunggu',
                    "{$user->nama} mengajukan {$label}, menunggu persetujuan Anda.",
                    email: true, emailJudul: 'Pengajuan Izin Menunggu Persetujuan'
                );
            });
        }

        return redirect()->route('karyawan.izin.dashboard')->with('status', 'Pengajuan izin berhasil dikirim.');
    }

    public function riwayat(Request $request)
    {
        $query = IzinRequest::where('user_id', Auth::id());

        if ($request->filled('periode')) {
            $bulan = \Carbon\Carbon::createFromFormat('Y-m', $request->periode);
            $query->whereMonth('tanggal', $bulan->month)->whereYear('tanggal', $bulan->year);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return view('karyawan.izin.riwayat', [
            'riwayat' => $query->with(['disetujuiOleh', 'atasanApprovedBy'])->orderByDesc('tanggal')->get(),
            'periode' => $request->get('periode', now()->format('Y-m')),
        ]);
    }

    public function notifikasi()
    {
        $notifications = NotificationWpm::where('user_id', Auth::id())
            ->whereIn('type', ['izin_menunggu', 'izin_disetujui', 'izin_ditolak'])
            ->orderByDesc('sent_at')
            ->get();

        return view('karyawan.izin.notifikasi', ['notifications' => $notifications]);
    }
}