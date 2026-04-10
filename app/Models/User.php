<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Puzzle;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
    'name', 'email', 'password', 'level', 'rating', 'bio', 'piece_style',
	];
	
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function diagnosticResults()
    {
        return $this->hasMany(DiagnosticResult::class);
    }

    public function gameSessions()
    {
        return $this->hasMany(GameSession::class);
    }

    public function userLessons()
    {
        return $this->hasMany(UserLesson::class);
    }

    public function progressStats()
    {
        return $this->hasMany(ProgressStat::class);
    }

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }
    
    public function solvedPuzzles()
	{
	    return $this->belongsToMany(Puzzle::class, 'user_puzzles')
	                ->withTimestamps();
	}    
}