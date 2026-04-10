<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Course;
use App\Models\Puzzle;
use App\Models\ProgressStat;

class DashboardController extends Controller
{
    public function index()
    {
        $user      = auth()->user()->fresh();
        $userLevel = $user->level ?? 1;

        // Курсы с количеством уроков и прогрессом пользователя
        $courses = Course::withCount('lessons')
            ->get()
            ->map(function ($course) use ($user) {
                $course->completed_count = $user->userLessons()
                    ->whereHas('lesson', fn($q) => $q->where('course_id', $course->id))
                    ->where('status', 'completed')
                    ->count();
                return $course;
            });

        // Уроки с курсом и pivot-статусом для текущего пользователя
        $lessons = Lesson::where('is_active', true)
            ->with('course')
            ->orderBy('order')
            ->get()
            ->map(function ($lesson) use ($user) {
                $userLesson = $user->userLessons()
                    ->where('lesson_id', $lesson->id)
                    ->first();
                $lesson->pivot = (object) [
                    'status' => $userLesson->status ?? 'locked'
                ];
                return $lesson;
            });

        // Задачи
        $puzzles = Puzzle::latest()->get();

        // Статистика
        $stats = ProgressStat::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->take(7)
            ->get()
            ->reverse()
            ->values();

        $completed    = $user->userLessons()->where('status', 'completed')->count();
        $totalLessons = Lesson::where('is_active', true)->count();
        
		$ratingWeekAgo = $user->rating - ($user->userLessons()->where('status', 'completed')
		    ->where('updated_at', '>=', now()->subWeek())->count() * 5)
		    - (\App\Models\Puzzle::whereHas('solvedByUsers', function($q) use ($user) {
		        $q->where('user_id', $user->id)
		          ->where('user_puzzles.created_at', '>=', now()->subWeek());
		    })->count() * 5);

        $ratingGain = $user->rating - $ratingWeekAgo;

		return view('dashboard.index', compact(
		    'user', 'courses', 'lessons', 'puzzles',
		    'stats', 'completed', 'totalLessons', 'ratingGain'
		));        
        
    }
}