<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AiChat;
use App\Models\AiChatMessage;
use App\Models\WeeklyCommitment;
use App\Services\AiCoachService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AiChatController extends Controller
{
    protected $aiCoachService;

    public function __construct(AiCoachService $aiCoachService)
    {
        $this->aiCoachService = $aiCoachService;
    }

    // 1. Ambil atau buat sesi chat minggu ini
    public function getOrInitChat($weeklyCommitmentId)
    {
        try {
            // Cari data commitment berdasarkan ID
            $commitment = WeeklyCommitment::find($weeklyCommitmentId);

            // Jika ID tidak ditemukan, ambil commitment terbaru milik user
            if (!$commitment) {
                $commitment = WeeklyCommitment::where('user_id', Auth::id())
                    ->latest()
                    ->first();
            }

            if (!$commitment) {
                return response()->json([
                    'error' => 'Data commitment tidak ditemukan'
                ], 404);
            }

            $chat = AiChat::firstOrCreate(
                [
                    'user_id' => Auth::id(),
                    'weekly_commitment_id' => $commitment->id,
                ],
                [
                    'title' => 'Diskusi Minggu Ke-' . date('W') . ' - ' . ($commitment->big_goal ?? 'Self Review')
                ]
            );

            // Jika chat baru dan belum ada pesan, buat pesan salam otomatis dari AI
            if ($chat->messages()->count() === 0) {
                $user = Auth::user();
                $name = $user->nama ?? $user->name ?? 'Rekan';
                $target = $commitment->big_goal ?? $commitment->target ?? 'Target Minggu Ini';
                $actual = $commitment->weeklyProgress->actual_sementara ?? $commitment->actualMingguan->actual_value ?? 0;

                $greeting = "Halo " . $name . "!\n\n" .
                    "Minggu ini targetmu adalah: " . $target . " dengan capaian sementara: " . $actual . ".\n\n" .
                    "Bagaimana evaluasi atau perasaanmu minggu ini? Apa kendala terbesar yang kamu hadapi?";

                $chat->messages()->create([
                    'sender' => 'ai',
                    'message' => $greeting
                ]);
            }

            return response()->json([
                'chat_id' => $chat->id,
                'title' => $chat->title,
                'messages' => $chat->messages()->orderBy('created_at', 'asc')->get()
            ]);

        } catch (\Throwable $e) {
            Log::error('AI Chat Init Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal memuat AI Chat: ' . $e->getMessage()
            ], 500);
        }
    }

    // 2. Ambil daftar seluruh riwayat chat user
    public function getChatHistoryList()
    {
        $histories = AiChat::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get(['id', 'title', 'created_at']);

        return response()->json([
            'histories' => $histories
        ]);
    }

    // 3. Load pesan dari sesi chat lama yang dipilih dari riwayat
    public function loadSpecificChat($chatId)
    {
        $chat = AiChat::where('user_id', Auth::id())
            ->findOrFail($chatId);

        return response()->json([
            'chat_id' => $chat->id,
            'title' => $chat->title,
            'messages' => $chat->messages()->orderBy('created_at', 'asc')->get()
        ]);
    }

    // 4. Kirim pesan & dapatkan balasan AI
    public function sendMessage(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|exists:ai_chats,id',
            'message' => 'required|string|max:2000'
        ]);

        try {
            $chat = AiChat::findOrFail($request->chat_id);

            if ($chat->user_id !== Auth::id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Simpan pesan user
            $userMessage = $chat->messages()->create([
                'sender' => 'user',
                'message' => $request->message
            ]);

            // Ambil histori percakapan sebagai konteks
            $chatHistory = $chat->messages()
                ->orderBy('created_at', 'asc')
                ->get(['sender', 'message'])
                ->toArray();

            // Minta respon dari AI Coach Service
            $aiResponseText = $this->aiCoachService->generateResponse(
                $chat->weeklyCommitment,
                $chatHistory,
                $request->message
            );

            // Simpan pesan AI
            $aiMessage = $chat->messages()->create([
                'sender' => 'ai',
                'message' => $aiResponseText
            ]);

            return response()->json([
                'user_message' => $userMessage,
                'ai_message' => $aiMessage
            ]);

        } catch (\Throwable $e) {
            Log::error('AI Send Message Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal memproses AI: ' . $e->getMessage()
            ], 500);
        }
    }
}