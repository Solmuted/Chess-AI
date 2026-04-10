<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
	{
	    Schema::table('lessons', function (Blueprint $table) {
	        // Колонка уже существует, добавляем только foreign key
	        $table->foreign('course_id')
	              ->references('id')
	              ->on('courses')
	              ->nullOnDelete();
	    });
	}
	
	public function down()
	{
	    Schema::table('lessons', function (Blueprint $table) {
	        $table->dropForeign(['course_id']);
	    });
	}
};
