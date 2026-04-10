<?php
namespace App\Http\Controllers;

use App\Models\ProgressStat;

class ProgressController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $stats = ProgressStat::where('user_id', $user->id)
                    ->orderBy('date')
                    ->get();

        $totalLessons = \App\Models\UserLesson::where('user_id', $user->id)
                            ->where('status', 'completed')
                            ->count();

        $totalPuzzles = $user->solvedPuzzles()->count(); // ← реальные данные

        $avgAccuracy  = $stats->avg('accuracy_pct') ?? 0;

        return view('progress.index', compact(
            'user', 'stats', 'totalLessons', 'totalPuzzles', 'avgAccuracy'
        ));
    }
}