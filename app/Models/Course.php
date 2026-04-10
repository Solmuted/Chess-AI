<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = ['title', 'difficulty', 'required_level', 'duration_minutes', 'is_new'];

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }
}