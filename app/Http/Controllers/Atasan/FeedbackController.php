<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\ApprovalComment;
use App\Models\NotificationWpm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $anggotaTim = User::where('atasan_id', Auth::id())->orderBy('nama')->get();

        $selectedId = $request->get('user', $anggotaTim->first()?->id);
        $pic = $anggotaTim->firstWhere('id', $selectedId);

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

        return view('atasan.feedback.index', [
            'anggotaTim' => $anggotaTim,
            'pic' => $pic,
            'commitment' => $commitment,
        ]);
    }

    public function store(Request $request, User $user)
    {
        abort_unless($user->atasan_id === Auth::id(), 403);

        $data = $request->validate([
            'weekly_commitment_id' => 'required|exists:weekly_commitment,id',
            'comment' => 'required|string',
        ]);

        ApprovalComment::create([
            'weekly_commitment_id' => $data['weekly_commitment_id'],
            'commented_by' => Auth::id(),
            'comment' => $data['comment'],
        ]);

        NotificationWpm::create([
            'user_id' => $user->id,
            'type' => 'feedback_atasan',
            'message' => 'Atasan memberi komentar pada weekly Anda: "'.str($data['comment'])->limit(80).'"',
            'is_read' => false,
            'sent_at' => now(),
        ]);

        return redirect()->route('atasan.feedback.index', ['user' => $user->id])->with('status', 'Komentar berhasil dikirim.');
    }
}