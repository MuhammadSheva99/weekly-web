<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\ApprovalComment;
use App\Models\Divisi;
use App\Models\NotificationWpm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $query = User::whereHas('role', fn ($q) => $q->whereIn('nama', ['Karyawan', 'Atasan']))
            ->with('divisi')
            ->orderBy('nama');

        if ($request->filled('divisi_id')) {
            $query->where('divisi_id', $request->divisi_id);
        }

        $daftarUser = $query->get();

        $selectedId = $request->get('user', $daftarUser->first()?->id);
        $pic = $daftarUser->firstWhere('id', $selectedId);

        $commitment = null;
        if ($pic) {
            $commitment = $pic->weeklyCommitment()
                ->with([
                    'targetMingguan.targetBulanan.kpi',
                    'weeklyProgress',
                    'selfReview',
                    'actualMingguan',
                    'approvalComments.commentedBy',
                ])
                ->latest()
                ->first();
        }

        return view('hrd.feedback.index', [
            'daftarUser' => $daftarUser,
            'divisiList' => Divisi::orderBy('nama')->get(),
            'pic' => $pic,
            'commitment' => $commitment,
        ]);
    }

    public function store(Request $request, User $user)
    {
        $data = $request->validate([
            'weekly_commitment_id' => 'required|exists:weekly_commitment,id',
            'comment' => 'required|string',
        ]);

        ApprovalComment::create([
            'weekly_commitment_id' => $data['weekly_commitment_id'],
            'commented_by' => Auth::id(),
            'comment' => $data['comment'],
        ]);

        \App\Services\NotificationService::send(
            $user, 'feedback_atasan',
            'HRD memberi komentar pada weekly Anda: "'.str($data['comment'])->limit(80).'"'
        );

        return redirect()->route('hrd.feedback.index', ['user' => $user->id])->with('status', 'Komentar berhasil dikirim.');
    }
}