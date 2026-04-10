<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
	{
	    Schema::create('puzzles', function (Blueprint $table) {
	        $table->id();
	        $table->string('title');
	        $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('easy');
	        $table->string('rating_range')->nullable(); // например "1200-1400"
	        $table->string('fen')->nullable();          // позиция на доске
	        $table->string('solution')->nullable();     // правильный ход
	        $table->timestamps();
	    });
	}
	
	public function down()
	{
	    Schema::dropIfExists('puzzles');
	}
};
