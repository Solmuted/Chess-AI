<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Puzzle;

class PuzzleSeeder extends Seeder
{
    public function run()
    {
        $puzzles = [

            // ══════════════════════════════
            // ЛЁГКИЕ — Mate in 1
            // ══════════════════════════════
            [
                'title'        => 'Мат в 1: Ладья',
                'difficulty'   => 'easy',
                'rating_range' => '600-900',
                'fen'          => '6k1/5ppp/8/8/8/8/5PPP/4R1K1 w - - 0 1',
                'solution'     => 'e1e8',
                // Ладья e1 → e8#
            ],
            [
                'title'        => 'Мат в 1: Ферзь',
                'difficulty'   => 'easy',
                'rating_range' => '600-900',
                'fen'          => '6k1/5ppp/8/8/8/8/5PPP/5QK1 w - - 0 1',
                'solution'     => 'f1f8',
                // Ферзь f1 → f8#
            ],
            [
                'title'        => 'Мат в 1: Ладья на 8-й',
                'difficulty'   => 'easy',
                'rating_range' => '600-900',
                'fen'          => '7k/5ppp/8/8/8/8/5PPP/3R2K1 w - - 0 1',
                'solution'     => 'd1d8',
                // Ладья d1 → d8#
            ],
            [
                'title'        => 'Мат в 1: Ферзь с фланга',
                'difficulty'   => 'easy',
                'rating_range' => '700-1000',
                'fen'          => '6k1/6pp/6p1/8/8/8/6PP/6QK w - - 0 1',
                'solution'     => 'g1g6',
                // Ферзь g1 → g6#
            ],
            [
                'title'        => 'Мат в 1: Конь',
                'difficulty'   => 'easy',
                'rating_range' => '700-1000',
                'fen'          => '5k2/8/4K3/8/8/8/8/5N2 w - - 0 1',
                'solution'     => 'f1e3',
                // Конь f1 → e3, затем будет мат (простая вилка)
            ],
            [
                'title'        => 'Мат в 1: Слон',
                'difficulty'   => 'easy',
                'rating_range' => '700-1000',
                'fen'          => '7k/6pp/8/8/8/8/6PP/5BK1 w - - 0 1',
                'solution'     => 'f1c4',
                // Слон f1 → c4, диагональная атака
            ],
            [
                'title'        => 'Мат в 1: Ладья h-файл',
                'difficulty'   => 'easy',
                'rating_range' => '800-1000',
                'fen'          => '7k/7p/7P/8/8/8/8/6RK w - - 0 1',
                'solution'     => 'g1g8',
                // Ладья g1 → g8#
            ],
            [
                'title'        => 'Удар на последней горизонтали',
                'difficulty'   => 'easy',
                'rating_range' => '800-1100',
                'fen'          => '3r2k1/5ppp/8/8/8/8/5PPP/3R2K1 w - - 0 1',
                'solution'     => 'd1d8',
                // Ладья бьёт ладью d8#
            ],

            // ══════════════════════════════
            // ЛЁГКИЕ — Тактика (вилки, связки)
            // ══════════════════════════════
            [
                'title'        => 'Вилка конём',
                'difficulty'   => 'easy',
                'rating_range' => '900-1100',
                'fen'          => 'r3k3/8/8/8/8/8/8/2N1K3 w - - 0 1',
                // Конь c1 → b3 → атакует ладью и короля
                'solution'     => 'c1b3',
            ],
            [
                'title'        => 'Вилка ферзём',
                'difficulty'   => 'easy',
                'rating_range' => '900-1100',
                'fen'          => 'r3k3/8/8/8/8/8/8/4KQ2 w - - 0 1',
                'solution'     => 'f1a6',
                // Ферзь атакует и короля и ладью
            ],
            [
                'title'        => 'Связка слоном',
                'difficulty'   => 'easy',
                'rating_range' => '900-1200',
                'fen'          => '4k3/4r3/8/8/8/8/4R3/3BK3 w - - 0 1',
                'solution'     => 'd1a4',
                // Слон связывает ладью с королём
            ],

            // ══════════════════════════════
            // СРЕДНИЕ — Мат в 2
            // ══════════════════════════════
            [
                'title'        => 'Мат в 2: Двойной удар',
                'difficulty'   => 'medium',
                'rating_range' => '1100-1300',
                'fen'          => '5rk1/5ppp/8/8/8/8/5PPP/3R2K1 w - - 0 1',
                'solution'     => 'd1d8',
                // Ладья бьёт ладью → мат следующим ходом
            ],
            [
                'title'        => 'Мат в 2: Ферзь и ладья',
                'difficulty'   => 'medium',
                'rating_range' => '1100-1400',
                'fen'          => '6k1/5ppp/8/8/8/5R2/5PPP/6QK w - - 0 1',
                'solution'     => 'g1d4',
                // Ферзь выходит на диагональ
            ],
            [
                'title'        => 'Шпилька',
                'difficulty'   => 'medium',
                'rating_range' => '1200-1400',
                'fen'          => '4k3/4r3/8/4R3/8/8/8/4K3 w - - 0 1',
                'solution'     => 'e5e8',
                // Ладья e5 → e8 шах, бьёт ладью
            ],
            [
                'title'        => 'Рентген (скользящая атака)',
                'difficulty'   => 'medium',
                'rating_range' => '1200-1500',
                'fen'          => '4k3/8/8/8/8/8/8/R3K3 w Q - 0 1',
                'solution'     => 'a1a8',
                // Ладья a1 → a8#
            ],
            [
                'title'        => 'Промежуточный ход',
                'difficulty'   => 'medium',
                'rating_range' => '1300-1500',
                'fen'          => '6k1/5r1p/6p1/8/8/6P1/5R1P/6K1 w - - 0 1',
                'solution'     => 'f2f7',
                // Ладья бьёт ладью с шахом
            ],
            [
                'title'        => 'Вскрытый шах',
                'difficulty'   => 'medium',
                'rating_range' => '1300-1600',
                'fen'          => '4k3/8/8/3Bb3/8/8/8/4K3 w - - 0 1',
                'solution'     => 'd5e6',
                // Слон бьёт слона с шахом
            ],
            [
                'title'        => 'Двойной шах',
                'difficulty'   => 'medium',
                'rating_range' => '1400-1600',
                'fen'          => '5k2/8/4NK2/8/8/8/8/8 w - - 0 1',
                'solution'     => 'e6d8',
                // Конь → d8 двойной шах
            ],
            [
                'title'        => 'Отвлечение',
                'difficulty'   => 'medium',
                'rating_range' => '1300-1500',
                'fen'          => '3r2k1/5ppp/8/8/8/8/5PPP/3RR1K1 w - - 0 1',
                'solution'     => 'd1d8',
                // Ладья отвлекает защитника
            ],
            [
                'title'        => 'Вскрытие линии',
                'difficulty'   => 'medium',
                'rating_range' => '1200-1400',
                'fen'          => '6k1/4Rppp/8/8/8/8/5PPP/6K1 w - - 0 1',
                'solution'     => 'e7e8',
                // Ладья → e8#
            ],

            // ══════════════════════════════
            // СРЕДНИЕ — Выигрыш материала
            // ══════════════════════════════
            [
                'title'        => 'Вилка конём (центр)',
                'difficulty'   => 'medium',
                'rating_range' => '1100-1300',
                'fen'          => 'r1bqkb1r/pppp1ppp/2n2n2/4p3/4P3/2N2N2/PPPP1PPP/R1BQKB1R w KQkq - 4 4',
                'solution'     => 'f3e5',
                // Конь бьёт пешку e5, вилка
            ],
            [
                'title'        => 'Связка по диагонали',
                'difficulty'   => 'medium',
                'rating_range' => '1200-1400',
                'fen'          => 'rnbqk2r/pppp1ppp/4pn2/8/1bPP4/2N5/PP2PPPP/R1BQKBNR w KQkq - 2 4',
                'solution'     => 'd1d2',
                // Ферзь защищает от связки
            ],

            // ══════════════════════════════
            // СЛОЖНЫЕ — Комбинации
            // ══════════════════════════════
            [
                'title'        => 'Жертва слона',
                'difficulty'   => 'hard',
                'rating_range' => '1500-1800',
                'fen'          => 'r1bk3r/ppp2ppp/2np4/4p3/2B1P1b1/2N2N2/PPPP1PPP/R1BQK2R w KQ - 0 1',
                'solution'     => 'f3e5',
                // Конь бьёт пешку — жертва с атакой
            ],
            [
                'title'        => 'Мат Легаля',
                'difficulty'   => 'hard',
                'rating_range' => '1500-1700',
                'fen'          => 'r1bqkb1r/pppp1ppp/2n2n2/4p3/2B1P3/5N2/PPPP1PPP/RNBQK2R w KQkq - 4 4',
                'solution'     => 'f3e5',
                // Конь → e5 жертвует ферзя (псевдо мат Легаля)
            ],
            [
                'title'        => 'Мат Эпаульет',
                'difficulty'   => 'hard',
                'rating_range' => '1600-1900',
                'fen'          => '3rk2r/ppp2ppp/8/8/8/8/PPP2PPP/3QK2R w K - 0 1',
                'solution'     => 'd1d8',
                // Ферзь → d8# (ладьи блокируют собственного короля)
            ],
            [
                'title'        => 'Жертва ладьи',
                'difficulty'   => 'hard',
                'rating_range' => '1600-1900',
                'fen'          => '6k1/5ppp/4p3/8/8/4P3/5PPP/4RRK1 w - - 0 1',
                'solution'     => 'e1e6',
                // Ладья жертвует себя → вскрытие
            ],
            [
                'title'        => 'Мат в 2: Спёртый мат',
                'difficulty'   => 'hard',
                'rating_range' => '1700-2000',
                'fen'          => '6rk/5Npp/8/8/8/8/6PP/6K1 w - - 0 1',
                'solution'     => 'f7h6',
                // Конь → h6+ → спёртый мат
            ],
            [
                'title'        => 'Zugzwang',
                'difficulty'   => 'hard',
                'rating_range' => '1700-2000',
                'fen'          => '8/8/4k3/8/4K3/8/8/4R3 w - - 0 1',
                'solution'     => 'e1e6',
                // Ладья загоняет в цугцванг
            ],
            [
                'title'        => 'Мельница',
                'difficulty'   => 'hard',
                'rating_range' => '1800-2100',
                'fen'          => '2kr4/ppp5/8/8/8/8/PPP5/2KR4 w - - 0 1',
                'solution'     => 'd1d8',
                // Ладья → d8+ начинает серию шахов
            ],
            [
                'title'        => 'Греческий подарок',
                'difficulty'   => 'hard',
                'rating_range' => '1700-2000',
                'fen'          => 'r1bq1rk1/ppp2ppp/2np1n2/2b1p3/2B1P3/2NP1N2/PPP2PPP/R1BQK2R w KQ - 0 1',
                'solution'     => 'c4h7',
                // Классическая жертва слона на h7
            ],
        ];

        foreach ($puzzles as $p) {
            Puzzle::create($p);
        }
    }
}