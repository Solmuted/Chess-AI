<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DiagnosticResult extends Model
{
    protected $fillable = [
        'user_id', 'score', 'level_assigned',
        'questions_json', 'answers_json',
    ];

    protected $casts = [
        'questions_json' => 'array',
        'answers_json'   => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}