<?php
namespace App\Http\Controllers;

use App\Models\Puzzle;
use App\Models\ProgressStat;
use Illuminate\Http\Request;

class PuzzleController extends Controller
{
    // Список задач
    public function index()
    {
        $user = auth()->user();

        $puzzles = Puzzle::latest()->get()->map(function ($puzzle) use ($user) {
            $puzzle->is_solved = $user->solvedPuzzles()
                ->where('puzzle_id', $puzzle->id)->exists();
            return $puzzle;
        });

        $solvedCount = $user->solvedPuzzles()->count();
        $totalCount  = Puzzle::count();

        return view('puzzles.index', compact('puzzles', 'solvedCount', 'totalCount'));
    }

    // Страница одной задачи
    public function show($id)
    {
        $puzzle   = Puzzle::findOrFail($id);
        $user     = auth()->user();
        $isSolved = $user->solvedPuzzles()->where('puzzle_id', $id)->exists();

        return view('puzzles.show', compact('puzzle', 'isSolved'));
    }

    // Проверка хода (AJAX)
    public function check(Request $request, $id)
	{
	    $puzzle   = Puzzle::findOrFail($id);
	    $move     = strtolower(trim($request->input('move')));
	    $solution = strtolower(trim($puzzle->solution));
	
	    $correct = ($move === $solution);
	    $user    = auth()->user()->fresh();
	
	    // Увеличиваем счётчик попыток всегда
	    $userPuzzle = \DB::table('user_puzzles')
	        ->where('user_id', $user->id)
	        ->where('puzzle_id', $puzzle->id)
	        ->first();
	
	    if ($userPuzzle) {
	        \DB::table('user_puzzles')
	            ->where('user_id', $user->id)
	            ->where('puzzle_id', $puzzle->id)
	            ->increment('attempts');
	        $attempts = $userPuzzle->attempts + 1;
	    } else {
	        \DB::table('user_puzzles')->insert([
	            'user_id'    => $user->id,
	            'puzzle_id'  => $puzzle->id,
	            'solved'     => false,
	            'attempts'   => 1,
	            'created_at' => now(),
	            'updated_at' => now(),
	        ]);
	        $attempts = 1;
	    }
	
	    if ($correct && !($userPuzzle?->solved)) {
	        // Записать как решённую
	        $user->solvedPuzzles()->syncWithoutDetaching([$puzzle->id]);
	        \DB::table('user_puzzles')
	            ->where('user_id', $user->id)
	            ->where('puzzle_id', $puzzle->id)
	            ->update(['solved' => true]);
	
	        // +5 к рейтингу
	        $user->increment('rating', 5);
	
	        // Точность: решил с первой попытки = 100%, иначе меньше
	        $accuracy = $attempts === 1 ? 100 : round(100 / $attempts);
	
	        // Записать в progress_stats
	        ProgressStat::updateOrCreate(
	            [
	                'user_id' => $user->id,
	                'date'    => now()->toDateString(),
	            ],
	            [
	                'lessons_done'   => 0,
	                'puzzles_solved' => 0,
	                'accuracy_pct'   => 0,
	                'rating_change'  => 0,
	            ]
	        );
	
	        ProgressStat::where('user_id', $user->id)
	            ->where('date', now()->toDateString())
	            ->increment('puzzles_solved');
	
	        ProgressStat::where('user_id', $user->id)
	            ->where('date', now()->toDateString())
	            ->increment('rating_change', 5);
	
	        // Пересчитать среднюю точность за сегодня
	        $todayStat   = ProgressStat::where('user_id', $user->id)
	                          ->where('date', now()->toDateString())
	                          ->first();
	        $solvedToday = $todayStat->puzzles_solved;
	        $oldAccuracy = $todayStat->accuracy_pct * ($solvedToday - 1);
	        $newAccuracy = round(($oldAccuracy + $accuracy) / $solvedToday, 1);
	
	        $todayStat->update(['accuracy_pct' => $newAccuracy]);
	
	        // Streak
	        $solvedToday = $user->solvedPuzzles()
	            ->wherePivot('created_at', '>=', now()->startOfDay())
	            ->count();
	
	        if ($solvedToday === 1) {
	            $newStreak  = ($user->streak ?? 0) + 1;
	            $bestStreak = max($newStreak, $user->best_streak ?? 0);
	            $user->update([
	                'streak'      => $newStreak,
	                'best_streak' => $bestStreak,
	            ]);
	        }
	    }
	
	    return response()->json([
	        'correct'  => $correct,
	        'solution' => $solution,
	    ]);
	}
}