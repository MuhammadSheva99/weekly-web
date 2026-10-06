<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\KpiMaster;
use App\Models\TargetBulanan;
use App\Models\TargetMingguan;
use App\Models\User;
use App\Support\KpiFormat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class KpiMasterController extends Controller
{
    protected const SATUAN = ['%', 'Rupiah', 'Angka', 'Hari', 'Bulan', 'Menit'];
    protected const POLA = ['maximize', 'minimize'];
    protected const ROLE_ASSIGNABLE = ['Karyawan', 'Atasan', 'HRD'];

    public function index(Request $request)
    {
        $divisiList = Divisi::orderBy('nama')->get();
        $adaKpi = KpiMaster::pluck('divisi_id')->unique();

        $divisiId = $request->get('divisi_id');
        if (! $divisiId || ! $divisiList->contains('id', $divisiId)) {
            $divisiId = $divisiList->first(fn ($d) => $adaKpi->contains($d->id))?->id ?? $divisiList->first()?->id;
        }

        $periode = (string) $request->get('periode', now()->format('Y-m'));
        if (! preg_match('/^\d{4}-\d{2}$/', $periode)) {
            $periode = now()->format('Y-m');
        }
        $periodeDate = Carbon::createFromFormat('Y-m', $periode)->startOfMonth();
        $tampilNonaktif = $request->boolean('nonaktif');

        $kpiList = KpiMaster::where('divisi_id', $divisiId)
            ->withCount('targetBulanan')
            ->when(! $tampilNonaktif, fn ($q) => $q->where('is_active', true))
            ->orderBy('id')
            ->get();

        $targetPerKpi = TargetBulanan::whereIn('kpi_id', $kpiList->pluck('id'))
            ->whereYear('periode', $periodeDate->year)
            ->whereMonth('periode', $periodeDate->month)
            ->get()
            ->groupBy('kpi_id');

        foreach ($kpiList as $kpi) {
            $rows = $targetPerKpi->get($kpi->id, collect());
            $bobotSet = $rows->pluck('bobot')->map(fn ($v) => (float) $v)->unique()->values();
            $targetSet = $rows->pluck('nilai_target')->map(fn ($v) => (float) $v)->unique()->values();

            $kpi->jumlah_orang = $rows->count();
            $kpi->bobot_nilai = $bobotSet->first();
            $kpi->bobot_beragam = $bobotSet->count() > 1;
            $kpi->target_nilai = $targetSet->first();
            $kpi->target_beragam = $targetSet->count() > 1;
            $kpi->target_teks = $targetSet->isEmpty() ? null : KpiFormat::tampil($targetSet->first(), $kpi->satuan);

            $kpi->bobot_input = $bobotSet->isEmpty() ? '' : $this->angkaInput($bobotSet->first());
            $kpi->target_input = $targetSet->isEmpty() ? '' : $this->angkaInput(
                $kpi->satuan === '%' ? $targetSet->first() * 100 : $targetSet->first()
            );
        }

        return view('admin.kpi-master.index', [
            'kpiList' => $kpiList,
            'divisiList' => $divisiList,
            'divisiId' => $divisiId,
            'periode' => $periode,
            'tampilNonaktif' => $tampilNonaktif,
            'totalBobot' => $kpiList->where('is_active', true)->sum(fn ($k) => (float) ($k->bobot_nilai ?? 0)),
            'kraList' => $kpiList->pluck('key_result_area')->filter()->unique()->values(),
            'satuanList' => self::SATUAN,
            'polaList' => self::POLA,
        ]);
    }

    public function store(Request $request)
    {
        [$attrs, $bobot, $target, $periode] = $this->validasi($request);

        if ($this->namaSudahAda($attrs)) {
            return back()->withErrors(['nama_kpi' => 'KPI dengan nama ini sudah ada di divisi tersebut.'])->withInput();
        }

        $jumlah = 0;
        DB::transaction(function () use ($attrs, $bobot, $target, $periode, &$kpi, &$jumlah) {
            $kpi = KpiMaster::create($attrs);
            if ($bobot !== null) {
                $jumlah = $this->terapkanTarget($kpi, $periode, $bobot, $target);
            }
        });

        $info = $jumlah ? " Target diterapkan ke {$jumlah} orang." : ' Belum ada target yang ditetapkan.';

        return $this->kembali($attrs['divisi_id'], $periode, "KPI '{$kpi->nama_kpi}' berhasil ditambahkan.".$info);
    }

    public function update(Request $request, KpiMaster $kpiMaster)
    {
        [$attrs, $bobot, $target, $periode] = $this->validasi($request);

        if ($this->namaSudahAda($attrs, $kpiMaster)) {
            return back()->withErrors(['nama_kpi' => 'KPI dengan nama ini sudah ada di divisi tersebut.'])->withInput();
        }

        // Satuan & divisi dikunci kalau KPI sudah punya target: angka lama
        // (misal 0.2 = 20%) akan salah arti kalau satuannya diganti.
        $dipakai = $kpiMaster->targetBulanan()->count();
        if ($dipakai > 0 && ($attrs['satuan'] !== $kpiMaster->satuan || $attrs['divisi_id'] !== $kpiMaster->divisi_id)) {
            return back()->withErrors([
                'satuan' => "Satuan dan divisi tidak bisa diubah karena KPI ini sudah dipakai di {$dipakai} target. Buat KPI baru, lalu nonaktifkan yang lama.",
            ])->withInput();
        }

        $jumlah = 0;
        DB::transaction(function () use ($kpiMaster, $attrs, $bobot, $target, $periode, &$jumlah) {
            $kpiMaster->update($attrs);
            if ($bobot !== null) {
                $jumlah = $this->terapkanTarget($kpiMaster, $periode, $bobot, $target);
            }
        });

        $info = $jumlah ? " Bobot & target periode {$periode} diperbarui untuk {$jumlah} orang." : '';

        return $this->kembali($kpiMaster->divisi_id, $periode, "KPI '{$kpiMaster->nama_kpi}' berhasil diperbarui.".$info);
    }

    public function destroy(Request $request, KpiMaster $kpiMaster)
    {
        $target = $kpiMaster->targetBulanan()->count();
        $performa = $kpiMaster->kpiPerformance()->count();

        if ($target > 0 || $performa > 0) {
            return back()->withErrors([
                'hapus' => "KPI '{$kpiMaster->nama_kpi}' tidak bisa dihapus karena sudah dipakai ({$target} target, {$performa} data performa). Nonaktifkan saja lewat tombol Edit (hilangkan centang Aktif).",
            ]);
        }

        $nama = $kpiMaster->nama_kpi;
        $kpiMaster->delete();

        return back()->with('status', "KPI '{$nama}' berhasil dihapus.");
    }

    protected function validasi(Request $request): array
    {
        $request->merge(['pola' => strtolower((string) $request->input('pola'))]);

        $data = $request->validate([
            'divisi_id' => ['required', 'exists:divisi,id'],
            'periode' => ['required', 'date_format:Y-m'],
            'key_result_area' => ['nullable', 'string', 'max:255'],
            'nama_kpi' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
            'sumber_data' => ['nullable', 'string', 'max:255'],
            'satuan' => ['required', Rule::in(self::SATUAN)],
            'pola' => ['required', Rule::in(self::POLA)],
            'rumus_realisasi' => ['nullable', 'string', 'max:500'],
            'rumus_target' => ['nullable', 'string', 'max:500'],
            'bobot' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'target' => ['nullable', 'regex:/^[0-9.,]+$/'],
        ], [
            'target.regex' => 'Target hanya boleh angka. Pakai koma untuk desimal (contoh 12,5).',
        ]);

        $bobotIsi = filled($data['bobot'] ?? null);
        $targetIsi = filled($data['target'] ?? null);
        if ($bobotIsi xor $targetIsi) {
            throw ValidationException::withMessages(['bobot' => 'Isi bobot dan target sekaligus, atau kosongkan keduanya.']);
        }

        $attrs = [
            'divisi_id' => $data['divisi_id'],
            'key_result_area' => filled($data['key_result_area'] ?? null) ? trim($data['key_result_area']) : null,
            'nama_kpi' => trim($data['nama_kpi']),
            'deskripsi' => filled($data['deskripsi'] ?? null) ? trim($data['deskripsi']) : null,
            'sumber_data' => filled($data['sumber_data'] ?? null) ? trim($data['sumber_data']) : null,
            'satuan' => $data['satuan'],
            'pola' => $data['pola'],
            'rumus_realisasi' => filled($data['rumus_realisasi'] ?? null) ? trim($data['rumus_realisasi']) : null,
            'rumus_target' => filled($data['rumus_target'] ?? null) ? trim($data['rumus_target']) : null,
            'target_fleksibel' => $request->boolean('target_fleksibel'),
            'is_active' => $request->boolean('is_active'),
        ];

        $bobot = $bobotIsi ? (float) $data['bobot'] : null;
        $target = $targetIsi ? KpiFormat::simpan($data['target'], $data['satuan']) : null;

        return [$attrs, $bobot, $target, $data['periode']];
    }

    protected function namaSudahAda(array $attrs, ?KpiMaster $kecuali = null): bool
    {
        return KpiMaster::where('divisi_id', $attrs['divisi_id'])
            ->where('nama_kpi', $attrs['nama_kpi'])
            ->when($kecuali, fn ($q) => $q->where('id', '!=', $kecuali->id))
            ->exists();
    }

    /**
     * Terapkan bobot & target ke periode tertentu (dipecah 4 minggu).
     * Siapa yang kena:
     *  1. orang yang sudah punya target KPI ini di periode itu;
     *  2. kalau belum ada: orang yang punya target KPI lain di divisi ini di periode itu;
     *  3. kalau masih kosong: semua anggota divisi (Karyawan/Atasan/HRD).
     */
    protected function terapkanTarget(KpiMaster $kpi, string $periode, float $bobot, float $target): int
    {
        $periodeDate = Carbon::createFromFormat('Y-m', $periode)->startOfMonth();

        $dalamPeriode = fn ($q) => $q->whereYear('periode', $periodeDate->year)
            ->whereMonth('periode', $periodeDate->month);

        $userIds = TargetBulanan::where('kpi_id', $kpi->id)->where($dalamPeriode)->pluck('user_id')->unique();

        if ($userIds->isEmpty()) {
            $kpiDivisi = KpiMaster::where('divisi_id', $kpi->divisi_id)->pluck('id');
            $userIds = TargetBulanan::whereIn('kpi_id', $kpiDivisi)->where($dalamPeriode)->pluck('user_id')->unique();
        }

        if ($userIds->isEmpty()) {
            $userIds = User::where('divisi_id', $kpi->divisi_id)
                ->whereHas('role', fn ($q) => $q->whereIn('nama', self::ROLE_ASSIGNABLE))
                ->pluck('id');
        }

        foreach ($userIds->unique() as $userId) {
            $targetBulanan = TargetBulanan::updateOrCreate(
                ['kpi_id' => $kpi->id, 'user_id' => $userId, 'periode' => $periodeDate],
                ['nilai_target' => $target, 'bobot' => $bobot]
            );

            for ($minggu = 1; $minggu <= 4; $minggu++) {
                TargetMingguan::updateOrCreate(
                    ['target_bulanan_id' => $targetBulanan->id, 'minggu_ke' => $minggu],
                    ['nilai_target' => round($target / 4, 4)]
                );
            }
        }

        return $userIds->unique()->count();
    }

    /** Angka -> teks untuk kolom input: tanpa pemisah ribuan, koma desimal, tanpa nol di belakang. */
    protected function angkaInput($n): string
    {
        return rtrim(rtrim(number_format(round((float) $n, 4), 4, ',', ''), '0'), ',');
    }

    protected function kembali(string $divisiId, string $periode, string $pesan)
    {
        return redirect()
            ->route('admin.kpi-master.index', ['divisi_id' => $divisiId, 'periode' => $periode])
            ->with('status', $pesan);
    }
}