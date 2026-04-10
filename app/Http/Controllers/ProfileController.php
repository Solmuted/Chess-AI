<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\GameSession;
use App\Models\DiagnosticResult;
use App\Models\Course;

class ProfileController extends Controller
{
    public function index()
    {
        $user       = auth()->user();
        $games      = GameSession::where('user_id', $user->id)->count();
        $completed  = $user->userLessons()->where('status', 'completed')->count();
        $diagnostic = DiagnosticResult::where('user_id', $user->id)->latest()->first();
        $winGames   = GameSession::where('user_id', $user->id)
                        ->where('result', 'completed')->count();
        $coursesByLevel = Course::orderBy('id')
                            ->get()
                            ->groupBy('required_level')
                            ->map(fn($group) => $group->first());

        return view('profile.index', compact(
            'user', 'games', 'completed', 'diagnostic', 'winGames', 'coursesByLevel'
        ));
    }

    // Вкладка "Основное" — имя и био
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'bio'  => 'nullable|string|max:300',
        ]);

        $user->name = $request->name;
        $user->bio  = $request->bio;
        $user->save();

        return back()->with('success', 'Профиль обновлён!')->with('tab', 'general');
    }

    // Вкладка "Внешний вид" — стиль фигур
    public function updateStyle(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'piece_style' => 'required|string|in:cburnett,merida,alpha,pirouetti,chessnut,chess7,reillycraig,companion',
        ]);

        $user->piece_style = $request->piece_style;
        $user->save();

        return back()->with('success', 'Стиль фигур сохранён!')->with('tab', 'appearance');
    }

    // Вкладка "Безопасность" — смена пароля
    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Текущий пароль неверный'])
                ->with('tab', 'security');
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Пароль успешно изменён!')->with('tab', 'security');
    }

    // Удаление аккаунта
    public function destroy(Request $request)
    {
        $user = auth()->user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Аккаунт удалён.');
    }
}