<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\DiagnosticResult;

class DiagnosticController extends Controller
{
    private array $questions = [
        1  => ['text' => 'Какая фигура ходит только по диагонали?',
                'options' => ['Ладья','Слон','Конь','Ферзь'],
                'correct' => 'Слон', 'weight' => 1],
        2  => ['text' => 'Сколько клеток на шахматной доске?',
                'options' => ['32','48','64','128'],
                'correct' => '64', 'weight' => 1],
        3  => ['text' => 'Что такое рокировка?',
                'options' => [
                    'Ход конём через фигуру',
                    'Одновременный ход королём и ладьёй',
                    'Превращение пешки',
                    'Взятие на проходе',
                ],
                'correct' => 'Одновременный ход королём и ладьёй', 'weight' => 1],
        4  => ['text' => 'Что такое "взятие на проходе"?',
                'options' => [
                    'Ладья берёт пешку',
                    'Пешка берёт пешку противника после хода на 2 клетки',
                    'Конь перепрыгивает через пешку',
                    'Слон берёт ферзя',
                ],
                'correct' => 'Пешка берёт пешку противника после хода на 2 клетки', 'weight' => 2],
        5  => ['text' => 'В чём цель шахматной партии?',
                'options' => [
                    'Съесть всех фигур противника',
                    'Поставить мат королю',
                    'Дойти пешкой до конца',
                    'Захватить центр доски',
                ],
                'correct' => 'Поставить мат королю', 'weight' => 1],
        6  => ['text' => 'Что такое "вилка" в шахматах?',
                'options' => [
                    'Одновременное нападение на две фигуры',
                    'Защита короля двумя ладьями',
                    'Ход пешкой вперёд',
                    'Размен ферзей',
                ],
                'correct' => 'Одновременное нападение на две фигуры', 'weight' => 2],
        7  => ['text' => 'Какая фигура стоит дороже всего?',
                'options' => ['Ладья','Слон','Конь','Ферзь'],
                'correct' => 'Ферзь', 'weight' => 1],
        8  => ['text' => 'Что такое "связка"?',
                'options' => [
                    'Фигура не может ходить не открыв короля под удар',
                    'Две пешки рядом',
                    'Конь и слон вместе атакуют',
                    'Рокировка в длинную сторону',
                ],
                'correct' => 'Фигура не может ходить не открыв короля под удар', 'weight' => 2],
        9  => ['text' => 'Что такое эндшпиль?',
                'options' => [
                    'Начало партии',
                    'Середина партии',
                    'Заключительная стадия партии',
                    'Быстрая победа в дебюте',
                ],
                'correct' => 'Заключительная стадия партии', 'weight' => 2],
        10 => ['text' => 'Как конь ходит?',
                'options' => [
                    'По диагонали',
                    'На любое количество клеток прямо',
                    'Буквой Г — 2+1 клетки',
                    'Только на одну клетку вперёд',
                ],
                'correct' => 'Буквой Г — 2+1 клетки', 'weight' => 1],
        11 => ['text' => 'Что такое "открытая линия" для ладьи?',
                'options' => [
                    'Линия без пешек обеих сторон',
                    'Линия с несколькими пешками',
                    'Диагональ слона',
                    'Центральная клетка доски',
                ],
                'correct' => 'Линия без пешек обеих сторон', 'weight' => 3],
        12 => ['text' => 'Что значит "оппозиция королей" в эндшпиле?',
                'options' => [
                    'Короли стоят рядом',
                    'Короли стоят напротив друг друга через одну клетку',
                    'Один король атакует другого',
                    'Короли на разных флангах',
                ],
                'correct' => 'Короли стоят напротив друг друга через одну клетку', 'weight' => 3],
        13 => ['text' => 'Какой принцип важен в дебюте?',
                'options' => [
                    'Быстро атаковать ферзём',
                    'Развивать фигуры и контролировать центр',
                    'Двигать крайние пешки',
                    'Сразу рокироваться ферзевым флангом',
                ],
                'correct' => 'Развивать фигуры и контролировать центр', 'weight' => 3],
        14 => ['text' => 'Что такое "цугцванг"?',
                'options' => [
                    'Быстрая атака в начале',
                    'Положение где любой ход ухудшает позицию',
                    'Жертва фигуры ради атаки',
                    'Защитная позиция короля',
                ],
                'correct' => 'Положение где любой ход ухудшает позицию', 'weight' => 3],
        15 => ['text' => 'Что такое "пешечная структура"?',
                'options' => [
                    'Количество пешек на доске',
                    'Расположение пешек определяющее план игры',
                    'Пешки защищающие короля',
                    'Проходные пешки в эндшпиле',
                ],
                'correct' => 'Расположение пешек определяющее план игры', 'weight' => 3],
    ];

    public function start()
    {
        $questions = $this->questions;
        return view('diagnostic.index', compact('questions'));
    }

    public function evaluate(Request $request): RedirectResponse
    {
        $score = 0;
        $answers = $request->input('answers', []);

        foreach ($answers as $qId => $answer) {
            if (isset($this->questions[$qId]) &&
                $this->questions[$qId]['correct'] === $answer) {
                $score += $this->questions[$qId]['weight'];
            }
        }

        $level = match(true) {
            $score <= 5  => 1,
            $score <= 12 => 2,
            $score <= 20 => 3,
            $score <= 28 => 4,
            default      => 5,
        };

        $rating = 400 + ($level * 150);

        auth()->user()->update([
            'level'  => $level,
            'rating' => $rating,
        ]);

        DiagnosticResult::create([
            'user_id'        => auth()->id(),
            'score'          => $score,
            'level_assigned' => $level,
            'questions_json' => array_keys($answers),
            'answers_json'   => $answers,
        ]);

        return redirect()->route('diagnostic.result')->with([
            'level'     => $level,
            'rating'    => $rating,
            'score'     => $score,
            'answers'   => $answers,
            'questions' => $this->questions,
        ]);
    }

    public function result()
    {
        if (!session('level')) {
            return redirect()->route('diagnostic');
        }
        return view('diagnostic.result', [
            'level'     => session('level'),
            'rating'    => session('rating'),
            'score'     => session('score'),
            'answers'   => session('answers'),
            'questions' => session('questions'),
        ]);
    }
}