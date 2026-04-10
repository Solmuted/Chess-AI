<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;

class CourseController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $courses = Course::withCount('lessons')->get()->map(function ($course) use ($user) {
            $course->completed_count = $user->userLessons()
                ->whereHas('lesson', fn($q) => $q->where('course_id', $course->id))
                ->where('status', 'completed')
                ->count();
            $course->is_locked = $user->level < $course->required_level;
            return $course;
        });

        return view('courses.index', compact('courses'));
    }

    public function show($id)
    {
        $course  = Course::with('lessons')->findOrFail($id);
        $user    = auth()->user();

        if ($user->level < $course->required_level) {
            return redirect('/courses')->with('error', 'Этот курс ещё заблокирован.');
        }

        $lessons = $course->lessons()->orderBy('order_in_course')->get()->map(function ($lesson) use ($user) {
            $ul = $user->userLessons()->where('lesson_id', $lesson->id)->first();
            $lesson->status = $ul->status ?? 'not_started';
            return $lesson;
        });

        $completed = $lessons->where('status', 'completed')->count();
        $progress  = $course->lessons_count > 0
            ? round($completed / $course->lessons->count() * 100)
            : 0;

        return view('courses.show', compact('course', 'lessons', 'completed', 'progress'));
    }
}
