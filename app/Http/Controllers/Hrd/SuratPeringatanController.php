<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\CompanyDocument;
use App\Models\Divisi;
use App\Models\NotificationWpm;
use App\Models\SuratPeringatan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SuratPeringatanController extends Controller
{
    // ============ Menu "Surat Peringatan" ============

    public function peraturan()
    {
        $dokumen = CompanyDocument::latest()->first();

        return view('hrd.surat-peringatan.peraturan', ['dokumen' => $dokumen]);
    }

    public function storeDokumen(Request $request)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:150',
            'file' => 'required|file|mimes:doc,docx,pdf|max:10240',
        ]);

        $lama = CompanyDocument::first();
        if ($lama) {
            Storage::disk('public')->delete($lama->file_path);
            $lama->delete();
        }

        $path = $request->file('file')->store('company-documents', 'public');

        CompanyDocument::create([
            'judul' => $data['judul'],
            'file_path' => $path,
            'uploaded_by' => Auth::id(),
        ]);

        return back()->with('status', 'Dokumen Peraturan Perusahaan berhasil diperbarui.');
    }

    // Tab "Riwayat Pelanggaran" di menu "Surat Peringatan"
    public function riwayatPelanggaran(Request $request)
    {
        return view('hrd.surat-peringatan.riwayat', $this->riwayatData($request));
    }

    // ============ Menu "SP Karyawan" ============

    public function terbitkanForm()
    {
        return view('hrd.sp-karyawan.terbitkan', [
            'allUsers' => User::whereHas('role', fn ($q) => $q->whereIn('nama', ['Karyawan', 'Atasan']))
                ->orderBy('nama')
                ->get(),
        ]);
    }

    // Tab "Riwayat Pelanggaran" di menu "SP Karyawan"
    public function riwayat(Request $request)
    {
        return view('hrd.sp-karyawan.riwayat', $this->riwayatData($request));
    }

    // Logic pengambilan data riwayat, dipakai bersama oleh 2 tab di atas
    protected function riwayatData(Request $request): array
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

        return [
            'daftar' => $query->orderByDesc('tanggal_terbit')->get(),
            'divisiList' => Divisi::orderBy('nama')->get(),
        ];
    }

    protected function generateNoSp(int $tahun): string
    {
        $urutanTerakhir = SuratPeringatan::whereYear('tanggal_terbit', $tahun)
            ->lockForUpdate()
            ->count();

        $nomorUrut = str_pad($urutanTerakhir + 1, 3, '0', STR_PAD_LEFT);

        return "SP-{$tahun}-{$nomorUrut}";
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'level' => 'required|in:SP1,SP2,SP3',
            'alasan' => 'required|string',
            'konsekuensi' => 'nullable|string',
            'tanggal_terbit' => 'required|date',
        ]);

        $tanggalTerbit = Carbon::parse($data['tanggal_terbit']);
        $karyawan = User::findOrFail($data['user_id']);

        $sp = DB::transaction(function () use ($data, $tanggalTerbit) {
            $noSp = $this->generateNoSp((int) $tanggalTerbit->format('Y'));

            return SuratPeringatan::create([
                'no_sp' => $noSp,
                'user_id' => $data['user_id'],
                'level' => $data['level'],
                'alasan' => $data['alasan'],
                'konsekuensi' => $data['konsekuensi'] ?? null,
                'tanggal_terbit' => $tanggalTerbit,
                'tanggal_berakhir' => $tanggalTerbit->copy()->addMonths(3),
                'diterbitkan_oleh' => Auth::id(),
            ]);
        });

        $levelAngka = (int) str_replace('SP', '', $sp->level);
        $levelBerikutnya = $levelAngka < 3 ? $levelAngka + 1 : null;

        $templatePath = storage_path('app/templates/sp-template.docx');
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

        $templateProcessor->setValue('nama_karyawan', $karyawan->nama);
        $templateProcessor->setValue('jabatan_karyawan', $karyawan->jabatan ?? $karyawan->role->nama);
        $templateProcessor->setValue('level_sp', (string) $levelAngka);
        $templateProcessor->setValue('level_sp_berikutnya', $levelBerikutnya ? (string) $levelBerikutnya : 'lebih lanjut');
        $templateProcessor->setValue('alasan_pelanggaran', $sp->alasan);
        $templateProcessor->setValue('konsekuensi', $sp->konsekuensi ?? '-');
        $templateProcessor->setValue('tanggal_terbit', $tanggalTerbit->translatedFormat('d F Y'));
        $templateProcessor->setValue('nama_hrd_penerbit', Auth::user()->nama);

        $fileName = 'sp-'.$sp->id.'.docx';
        $outputPath = storage_path('app/public/surat-peringatan/'.$fileName);

        if (! is_dir(dirname($outputPath))) {
            mkdir(dirname($outputPath), 0755, true);
        }

        $templateProcessor->saveAs($outputPath);

        $sp->update(['file_path' => 'surat-peringatan/'.$fileName]);

        NotificationWpm::create([
            'user_id' => $sp->user_id,
            'type' => 'sp_diterbitkan',
            'message' => "Anda menerima Surat Peringatan ({$sp->level}). Berlaku sampai {$sp->tanggal_berakhir->translatedFormat('d F Y')}.",
            'is_read' => false,
            'sent_at' => now(),
        ]);

        return redirect()->route('hrd.sp-karyawan.riwayat')->with('status', 'Surat Peringatan berhasil diterbitkan.');
    }

    public function download(SuratPeringatan $sp)
    {
        $user = Auth::user();
        $roleNama = $user->role->nama;

        $bolehLihat = $roleNama === 'HRD'
            || $sp->user_id === $user->id
            || ($roleNama === 'Atasan' && $sp->user->atasan_id === $user->id);

        abort_unless($bolehLihat, 403);
        abort_unless($sp->file_path, 404, 'Dokumen SP belum tersedia.');

        return Storage::disk('public')->download($sp->file_path, "Surat-Peringatan-{$sp->level}-{$sp->user->nama}.docx");
    }

    public function destroy(SuratPeringatan $sp)
    {
        if ($sp->file_path) {
            Storage::disk('public')->delete($sp->file_path);
        }

        $sp->delete();

        return back()->with('status', 'Surat Peringatan berhasil dihapus.');
    }
}