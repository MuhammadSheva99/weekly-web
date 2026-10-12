<?php

return [

    // Model & parameter OpenAI
    'model'       => env('AI_COACH_MODEL', 'gpt-4o-mini'),
    'temperature' => (float) env('AI_COACH_TEMPERATURE', 0.7),
    'max_tokens'  => (int) env('AI_COACH_MAX_TOKENS', 600),

    // Jumlah pesan riwayat terakhir yang dikirim ke AI (menekan biaya & risiko melebihi token)
    'max_history' => (int) env('AI_COACH_MAX_HISTORY', 20),

    // Lama cache referensi divisi (detik). Cache otomatis dibersihkan saat data diubah.
    'knowledge_cache_ttl' => (int) env('AI_COACH_KNOWLEDGE_TTL', 3600),

    // Setelah berapa giliran karyawan AI mulai merangkum dan memberi rencana aksi
    'wrap_up_after_turns' => (int) env('AI_COACH_WRAP_UP_TURNS', 4),

    // Dikembalikan jika OpenAI error/timeout
    'fallback_message' => 'Maaf, coach AI sedang tidak dapat merespons. '
        . 'Silakan coba kirim ulang pesan Anda beberapa saat lagi.',
];