<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use App\Models\GameSession;
use App\Models\ChatMessage;

class TrainerController extends Controller
{
    private string $model = 'llama-3.1-8b-instant';

	    public function setup()
		{
		    return view('trainer.setup');
		}
		
		public function index()
		{
		    $user  = auth()->user();
		    $color = request('color');
		
		    if (!$color || !in_array($color, ['white', 'black'])) {
		        return redirect('/trainer/setup');
		    }
		
		    $session = GameSession::create([
		        'user_id'    => $user->id,
		        'result'     => null,
		        'mode'       => 'trainer',
		        'fen'        => 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1',
		        'pgn'        => '',
		        'move_count' => 0,
		    ]);
		
		    $messages = collect();
		
		    return view('trainer.index', compact('user', 'session', 'messages', 'color'));
		}	
		
    public function chat(Request $request): JsonResponse
    {
        $user    = auth()->user();
        $session = GameSession::findOrFail($request->session_id);

        if ($request->fen) {
            $session->update([
                'fen'        => $request->fen,
                'pgn'        => $request->pgn ?? $session->pgn,
                'move_count' => $session->move_count + 1,
            ]);
        }

        ChatMessage::create([
            'user_id'         => $user->id,
            'game_session_id' => $session->id,
            'role'            => 'user',
            'content'         => $request->message,
        ]);

        $history = ChatMessage::where('game_session_id', $session->id)
                        ->latest()->take(6)->get()->reverse()
                        ->map(fn($m) => [
                            'role'    => $m->role,
                            'content' => $m->content,
                        ])->values()->toArray();

        $response = Http::withToken(config('services.groq.key'))
            ->timeout(30)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'       => $this->model,
                'messages'    => array_merge(
                    [['role' => 'system', 'content' => $this->buildSystemPrompt($user, $request)]],
                    $history,
                    [['role' => 'user', 'content' => $request->message]]
                ),
                'max_tokens'  => 200,
                'temperature' => 0.7,
            ]);

        if ($response->failed()) {
            return response()->json([
                'reply' => 'Ошибка: ' . $response->status() . ' — ' . $response->body()
            ], 500);
        }

        $reply = $response['choices'][0]['message']['content'] ?? 'Не удалось получить ответ.';

        ChatMessage::create([
            'user_id'         => $user->id,
            'game_session_id' => $session->id,
            'role'            => 'assistant',
            'content'         => $reply,
        ]);

        return response()->json(['reply' => $reply]);
    }

    public function move(Request $request): JsonResponse
	{
	    $session  = GameSession::findOrFail($request->session_id);
	    $playingAs = $request->playing_as ?? 'white'; // цвет игрока
	    $aiColor   = $playingAs === 'white' ? 'чёрных' : 'белых';
	    $aiSide    = $playingAs === 'white' ? 'black' : 'white';
	
	    $response = Http::withToken(config('services.groq.key'))
	        ->timeout(30)
	        ->post('https://api.groq.com/openai/v1/chat/completions', [
	            'model'    => $this->model,
	            'messages' => [
	                ['role' => 'system', 'content' =>
	                    "Ты играешь за {$aiColor}. FEN: {$request->fen}. " .
	                    "Ответь ТОЛЬКО ходом в формате UCI например e7e5. Только ход, без текста."],
	                ['role' => 'user', 'content' => 'Твой ход.'],
	            ],
	            'max_tokens'  => 10,
	            'temperature' => 0.3,
	        ]);
	
	    if ($response->failed()) {
	        return response()->json(['move' => null], 500);
	    }
	
	    $move = trim($response['choices'][0]['message']['content'] ?? '');
	    $move = preg_replace('/[^a-h1-8qrbn]/', '', strtolower($move));
	
	    return response()->json(['move' => $move]);
	}

    public function analyze_game(Request $request): JsonResponse
    {
        $user    = auth()->user();
        $session = GameSession::findOrFail($request->session_id);
        $pgn     = $request->pgn ?? $session->pgn;

        $response = Http::withToken(config('services.groq.key'))
            ->timeout(30)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'       => $this->model,
                'messages'    => [
                    ['role' => 'system', 'content' =>
                        "Ты тренер Гарри. Уровень ученика: {$user->level}/5. " .
                        "Разбери партию PGN: {$pgn}. " .
                        "Формат ответа:\n" .
                        "✅ ЛУЧШИЕ ХОДЫ: 1-2 хороших хода\n" .
                        "❌ ОШИБКИ: 1-2 главные ошибки\n" .
                        "⚠️ ЗЕВКИ: если были — укажи\n" .
                        "💡 СОВЕТ: один главный совет\n" .
                        "По-русски, кратко, до 8 предложений."],
                    ['role' => 'user', 'content' => 'Разбери мою партию.'],
                ],
                'max_tokens'  => 400,
                'temperature' => 0.5,
            ]);

        if ($response->failed()) {
            return response()->json(['analysis' => 'Не удалось получить анализ.'], 500);
        }

        $analysis = $response['choices'][0]['message']['content'] ?? 'Анализ недоступен.';
        $session->update(['result' => 'completed']);

        return response()->json(['analysis' => $analysis]);
    }

    public function analyze(Request $request): JsonResponse
    {
        $session = GameSession::findOrFail($request->session_id);
        $session->update(['fen' => $request->fen]);
        return response()->json(['status' => 'ok']);
    }

    private function buildSystemPrompt($user, $request): string
    {
        $fen = $request->fen ?? 'начальная позиция';
        $pgn = $request->pgn ?? '';

        return "Ты — шахматный тренер Гарри. Уровень ученика: {$user->level}/5. " .
               "FEN: {$fen}. PGN: {$pgn}. " .
               "Отвечай по-русски, кратко 2-3 предложения. " .
               "Хвали хорошие ходы, мягко указывай на ошибки, предлагай следующий шаг.";
    }
}