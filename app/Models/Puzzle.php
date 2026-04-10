<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Puzzle extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'difficulty', 'rating_range', 'fen', 'solution',
    ];

    // Связь: задача решена многими пользователями
    public function solvedByUsers()
    {
        return $this->belongsToMany(User::class, 'user_puzzles')
                    ->withTimestamps();
    }
}
