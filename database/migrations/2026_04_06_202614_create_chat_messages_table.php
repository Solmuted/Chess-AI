<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('game_session_id');
            $table->enum('role', ['user','assistant'])->default('user');
            $table->text('content');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('game_session_id')->references('id')->on('game_sessions')->cascadeOnDelete();
            $table->index(['user_id', 'game_session_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('chat_messages'); }
};