<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('recipient'); // Alıcı telefon numarası
            $table->text('content');     // Mesaj içeriği
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->string('external_message_id')->nullable(); // Webhook'tan dönen ID
            $table->timestamp('sent_at')->nullable(); // Gönderim zamanı
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
