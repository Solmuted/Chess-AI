<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_puzzles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('puzzle_id')->constrained()->cascadeOnDelete();
            $table->boolean('solved')->default(false);
            $table->integer('attempts')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'puzzle_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_puzzles');
    }
};