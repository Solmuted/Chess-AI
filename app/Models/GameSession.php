<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class GameSession extends Model
{
    protected $fillable = [
        'user_id', 'fen', 'pgn',
        'mode', 'result', 'move_count',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }
}