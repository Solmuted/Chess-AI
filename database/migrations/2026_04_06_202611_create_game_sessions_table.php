<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('game_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->text('fen')->nullable();
            $table->text('pgn')->nullable();
            $table->enum('mode', ['trainer','puzzle','free'])->default('trainer');
            $table->string('result')->nullable();
            $table->unsignedSmallInteger('move_count')->default(0);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
    public function down(): void { Schema::dropIfExists('game_sessions'); }
};