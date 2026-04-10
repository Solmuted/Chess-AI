<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Course;
use App\Models\UserLesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function show($id)
    {
        $lesson = Lesson::with('course')->findOrFail($id);
        $user   = auth()->user();

        // Создать или получить запись прогресса
        $userLesson = UserLesson::firstOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            ['status' => 'in_progress', 'attempts' => 0]
        );

        if ($userLesson->status === 'not_started') {
            $userLesson->update(['status' => 'in_progress']);
        }

        // Следующий и предыдущий урок в курсе
        $prev = null;
        $next = null;
        if ($lesson->course_id) {
            $prev = Lesson::where('course_id', $lesson->course_id)
                ->where('order_in_course', '<', $lesson->order_in_course)
                ->orderBy('order_in_course', 'desc')
                ->first();
            $next = Lesson::where('course_id', $lesson->course_id)
                ->where('order_in_course', '>', $lesson->order_in_course)
                ->orderBy('order_in_course')
                ->first();
        }

        // Прогресс по курсу
        $courseProgress = null;
        if ($lesson->course) {
            $total     = $lesson->course->lessons()->count();
            $completed = $user->userLessons()
                ->whereHas('lesson', fn($q) => $q->where('course_id', $lesson->course_id))
                ->where('status', 'completed')
                ->count();
            $courseProgress = $total > 0 ? round($completed / $total * 100) : 0;
        }

        return view('lessons.show', compact('lesson', 'userLesson', 'prev', 'next', 'courseProgress'));
    }

    public function complete(Request $request, $id)
{
    $lesson = Lesson::findOrFail($id);
    $user   = auth()->user();

    // Проверяем — вдруг уже завершён (защита от двойного нажатия)
    $existing = UserLesson::where('user_id', $user->id)
        ->where('lesson_id', $lesson->id)
        ->where('status', 'completed')
        ->first();

    if (!$existing) {
        UserLesson::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            [
                'status'       => 'completed',
                'completed_at' => now(),
                'score'        => 100,
            ]
        );

        // +3 к рейтингу
        $user->increment('rating', 3);

        // Записать в progress_stats (за сегодня)
        \App\Models\ProgressStat::updateOrCreate(
            [
                'user_id' => $user->id,
                'date'    => now()->toDateString(),
            ],
            [] // не перезаписываем если уже есть
        );

        \App\Models\ProgressStat::where('user_id', $user->id)
            ->where('date', now()->toDateString())
            ->increment('lessons_done');

        \App\Models\ProgressStat::where('user_id', $user->id)
            ->where('date', now()->toDateString())
            ->increment('rating_change', 3);
    }

    return response()->json([
        'success' => true,
        'next_id' => optional(
            Lesson::where('course_id', $lesson->course_id)
                ->where('order_in_course', '>', $lesson->order_in_course)
                ->orderBy('order_in_course')
                ->first()
        )->id,
    ]);
}
    
}
