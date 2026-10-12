<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\WeeklyCommitment;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    /**
     * Daftar minggu yang sudah diberi komentar oleh Atasan/HRD (satu submit = satu batch).
     */
    public function index()
    {
        $commitments = WeeklyCommitment::where('user_id', Auth::id())
            ->whereHas('approvalComments')
            ->with([
                'targetMingguan.targetBulanan.kpi',
                'approvalComments.commentedBy.role',
            ])
            ->orderByDesc('created_at')
            ->get();

        $feedbackMingguan = $commitments
            ->groupBy(fn ($c) => $c->batch_id ?? $c->id)
            ->map(function ($group) {
                $first = $group->sortBy('created_at')->first();
                $mingguKe = $first->targetMingguan->minggu_ke ?? null;

                $komentar = $this->kumpulkanKomentar($group);

                return (object) [
                    'key' => $first->batch_id ?? $first->id,
                    'label' => $first->created_at->translatedFormat('\M\i\n\g\g\u '.($mingguKe ?? '?').' - F Y'),
                    'terakhir' => $komentar->first()->waktu,
                    'komentar' => $komentar,
                ];
            })
            ->sortByDesc('terakhir')
            ->take(8)
            ->values();

        return view('karyawan.feedback.index', ['feedbackMingguan' => $feedbackMingguan]);
    }

    /**
     * Detail satu minggu (satu batch): Weekly Commitment, Weekly Progress, Self Review, lalu Feedback.
     */
    public function show(string $batch)
    {
        $commitments = WeeklyCommitment::where('user_id', Auth::id())
            ->where(fn ($q) => $q->where('batch_id', $batch)->orWhere('id', $batch))
            ->with([
                'targetMingguan.targetBulanan.kpi',
                'weeklyProgress',
                'selfReview',
                'actualMingguan',
                'approvalComments.commentedBy.role',
            ])
            ->get()
            ->sortBy('id')
            ->values();

        abort_if($commitments->isEmpty(), 404);

        $first = $commitments->first();
        $mingguKe = $first->targetMingguan->minggu_ke ?? null;
        $label = $first->created_at->translatedFormat('\M\i\n\g\g\u '.($mingguKe ?? '?').' - F Y');

        // Weekly Commitment: big goal / prioritas / output sama untuk semua KPI di batch ini.
        $prioritas = $first->prioritas;
        if (is_string($prioritas)) {
            $prioritas = json_decode($prioritas, true) ?: [];
        }
        $prioritas = array_values(array_filter((array) $prioritas));

        // Weekly Progress: catatan sama untuk semua KPI di batch ini.
        $catatan = $commitments->map->weeklyProgress->filter()->first();

        // Self Review: jawaban atas 6 pertanyaan (sama untuk semua KPI di batch ini).
        $pertanyaan = [
            'apa_berhasil' => 'Apa yang berhasil?',
            'apa_gagal' => 'Apa yang gagal?',
            'kenapa_gagal' => 'Kenapa gagal?',
            'apa_beda' => 'Apa yang seharusnya dilakukan berbeda?',
            'improvement_depan' => 'Apa improvement minggu depan?',
            'kritik_diri' => 'Kalau saya jadi atasan, apa yang akan saya kritik dari pekerjaan saya minggu ini?',
        ];
        $reviewCatatan = [];
        $review = $commitments->map->selfReview->filter()->first();
        if ($review) {
            foreach ($pertanyaan as $kolom => $judul) {
                if (filled($review->{$kolom})) {
                    $reviewCatatan[$judul] = $review->{$kolom};
                }
            }
        }

        $komentar = $this->kumpulkanKomentar($commitments);

        return view('karyawan.feedback.show', compact(
            'commitments', 'first', 'label', 'prioritas', 'catatan', 'reviewCatatan', 'komentar'
        ));
    }

    /** Semua komentar dari sekelompok commitment, terbaru di atas. */
    private function kumpulkanKomentar($commitments)
    {
        return $commitments->flatMap(function ($c) {
            $kpi = $c->targetMingguan->targetBulanan->kpi->nama_kpi ?? null;

            return $c->approvalComments->map(fn ($a) => (object) [
                'pemberi' => $a->commentedBy->nama ?? '-',
                'peran' => $a->commentedBy->role->nama ?? '',
                'kpi' => $kpi,
                'isi' => $a->comment,
                'waktu' => $a->created_at,
            ]);
        })->sortByDesc('waktu')->values();
    }
}