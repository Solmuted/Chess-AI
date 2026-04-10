<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('progress_stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('date')->index();
            $table->tinyInteger('lessons_done')->default(0);
            $table->tinyInteger('puzzles_solved')->default(0);
            $table->decimal('accuracy_pct', 5, 2)->default(0);
            $table->smallInteger('rating_change')->default(0);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unique(['user_id', 'date']);
        });
    }
    public function down(): void { Schema::dropIfExists('progress_stats'); }
};