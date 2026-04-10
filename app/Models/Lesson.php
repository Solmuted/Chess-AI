<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'title', 'description', 'level',
        'topic', 'content_blade', 'order', 'is_active',
    ];

    public function userLessons()
    {
        return $this->hasMany(UserLesson::class);
    }
    
    public function course()
	{
	    return $this->belongsTo(Course::class);
	}
}