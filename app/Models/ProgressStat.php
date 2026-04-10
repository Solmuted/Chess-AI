<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProgressStat extends Model
{
    protected $fillable = [
        'user_id', 'date', 'lessons_done',
        'puzzles_solved', 'accuracy_pct', 'rating_change',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}