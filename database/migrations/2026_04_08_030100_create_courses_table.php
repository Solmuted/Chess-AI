<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // database/migrations/2026_04_08_030342_create_courses_table.php
	public function up()
	{
	    Schema::create('courses', function (Blueprint $table) {
	        $table->id();
	        $table->string('title');
	        $table->enum('difficulty', ['beginner', 'intermediate', 'advanced'])->default('beginner');
	        $table->unsignedTinyInteger('required_level')->default(1);
	        $table->unsignedInteger('duration_minutes')->nullable();
	        $table->boolean('is_new')->default(false);
	        $table->timestamps();
	    });
	}
	
	public function down()
	{
	    Schema::dropIfExists('courses');
	}
};
