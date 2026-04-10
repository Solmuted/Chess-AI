<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DiagnosticController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PuzzleController;
use Illuminate\Http\Request;
use App\Http\Controllers\CourseController;

Route::get('/', function () {
    return view('welcome');
});

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Diagnostic
    Route::get('/diagnostic',        [DiagnosticController::class, 'start'])->name('diagnostic');
    Route::post('/diagnostic',       [DiagnosticController::class, 'evaluate'])->name('diagnostic.submit');
    Route::get('/diagnostic/result', [DiagnosticController::class, 'result'])->name('diagnostic.result');

    // Lessons
    Route::get('/lesson/{id}',           [LessonController::class, 'show']);
    Route::post('/lesson/{id}/complete', [LessonController::class, 'complete']);

    // Trainer
    Route::get('/trainer',               [TrainerController::class, 'index']);
    Route::get('/trainer/setup',         [TrainerController::class, 'setup']);
    Route::post('/trainer/chat',         [TrainerController::class, 'chat']);
    Route::post('/trainer/move',         [TrainerController::class, 'move']);
    Route::post('/trainer/analyze',      [TrainerController::class, 'analyze']);
    Route::post('/trainer/analyze-game', [TrainerController::class, 'analyze_game']);

    // Progress
    Route::get('/progress', [ProgressController::class, 'index']);
    
    // Tactical puzzles
	Route::get('/puzzles',            [PuzzleController::class, 'index'])->name('puzzles');
	Route::get('/puzzle/{id}',        [PuzzleController::class, 'show'])->name('puzzle.show');
	Route::post('/puzzle/{id}/check', [PuzzleController::class, 'check'])->name('puzzle.check');
	
	// AI proxy
	Route::post('/ai/chat', function (Request $request) {
    $messages = $request->input('messages', []);
    $system   = $request->input('system', '');

    $response = \Illuminate\Support\Facades\Http::withHeaders([
        'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
        'Content-Type'  => 'application/json',
    ])->post('https://api.groq.com/openai/v1/chat/completions', [
        'model'    => 'llama-3.1-8b-instant',
        'messages' => array_merge(
            [['role' => 'system', 'content' => $system]],
            $messages
        ),
        'max_tokens' => 512,
    ]);

    $data = $response->json();

    // Привести ответ к формату Anthropic чтобы фронт не менять
    return response()->json([
        'content' => [[
            'text' => $data['choices'][0]['message']['content'] ?? 'Нет ответа'
        ]]
    ]);
})->name('ai.chat');

    // Profile
	Route::get('/profile',          [ProfileController::class, 'index'])->name('profile');
	Route::put('/profile',          [ProfileController::class, 'update'])->name('profile.update');
	Route::put('/profile/style',    [ProfileController::class, 'updateStyle'])->name('profile.style');
	Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
	Route::delete('/profile',       [ProfileController::class, 'destroy'])->name('profile.destroy');
	
	// Courses
	Route::get('/courses',      [CourseController::class, 'index'])->name('courses');
	Route::get('/course/{id}',  [CourseController::class, 'show'])->name('course.show');
	    
    Route::get('/chess-tactics', function () {
    return view('chess-tactics');
	});
});