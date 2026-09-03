<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications_wpm', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->enum('type', [
                'reminder_commitment', 'reminder_progress', 'reminder_review',
                'alert_underperform', 'alert_belum_submit',
            ]);
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->dateTime('sent_at');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications_wpm');
    }
};