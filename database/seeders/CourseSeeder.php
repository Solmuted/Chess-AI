<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Lesson;

class CourseSeeder extends Seeder
{
    public function run()
    {
        // Курс 1 — Основы
        $c1 = Course::create([
            'title'            => 'Основы шахмат',
            'difficulty'       => 'beginner',
            'required_level'   => 1,
            'duration_minutes' => 60,
            'is_new'           => false,
        ]);
        $lessons1 = [
            ['Как ходят фигуры',        'Изучи правила движения каждой фигуры: ладья, слон, ферзь, конь, король и пешка.', 'basics'],
            ['Ценность фигур',          'Узнай сколько стоит каждая фигура и как правильно менять материал.', 'basics'],
            ['Цель игры: шах и мат',    'Что такое шах, мат и пат. Как поставить мат одинокому королю.', 'basics'],
            ['Начало партии',           'Три главных принципа дебюта: развитие фигур, контроль центра, безопасность короля.', 'opening'],
            ['Рокировка',               'Когда и как делать рокировку. Почему это важно для защиты короля.', 'basics'],
        ];
        foreach ($lessons1 as $i => [$title, $desc, $topic]) {
            Lesson::create([
                'course_id'   => $c1->id,
                'title'       => $title,
                'description' => $desc,
                'topic'       => $topic,
                'level'       => 1,
                'order'       => $i + 1,
                'is_active'   => true,
                'order_in_course' => $i + 1,
            ]);
        }

        // Курс 2 — Тактика для начинающих
        $c2 = Course::create([
            'title'            => 'Тактика для начинающих',
            'difficulty'       => 'beginner',
            'required_level'   => 1,
            'duration_minutes' => 90,
            'is_new'           => true,
        ]);
        $lessons2 = [
            ['Вилка',             'Атака двух фигур одновременно. Конь, ферзь и пешка — мастера вилки.', 'tactics'],
            ['Связка',            'Фигура не может уйти, не подставив более ценную за собой.', 'tactics'],
            ['Шпилька (рентген)', 'Атака через фигуру: бьёшь одну — выигрываешь другую.', 'tactics'],
            ['Двойной удар',      'Как создать две угрозы одним ходом так, чтобы соперник не успел защититься.', 'tactics'],
            ['Мат в 1 ход',       'Тренировка распознавания матовых позиций с одного хода.', 'tactics'],
        ];
        foreach ($lessons2 as $i => [$title, $desc, $topic]) {
            Lesson::create([
                'course_id'   => $c2->id,
                'title'       => $title,
                'description' => $desc,
                'topic'       => $topic,
                'level'       => 1,
                'order'       => 10 + $i + 1,
                'is_active'   => true,
                'order_in_course' => $i + 1,
            ]);
        }

        // Курс 3 — Дебюты
        $c3 = Course::create([
            'title'            => 'Популярные дебюты',
            'difficulty'       => 'intermediate',
            'required_level'   => 2,
            'duration_minutes' => 120,
            'is_new'           => false,
        ]);
        $lessons3 = [
            ['Итальянская партия',    'Классический дебют 1.e4 e5 2.Nf3 Nc6 3.Bc4. Принципы и ловушки.', 'opening'],
            ['Сицилианская защита',   '1.e4 c5 — самый популярный ответ на 1.e4. Идеи за чёрных.', 'opening'],
            ['Дебют ферзевых пешек',  '1.d4 d5 — солидная система с борьбой за центр.', 'opening'],
            ['Английское начало',     '1.c4 — фланговый дебют с гибкими возможностями.', 'opening'],
            ['Ловушки в дебюте',      'Детский мат, ловушка Легаля и другие опасные ловушки для соперника.', 'opening'],
            ['Типичные планы',        'Как переходить из дебюта в миттельшпиль с конкретным планом.', 'opening'],
        ];
        foreach ($lessons3 as $i => [$title, $desc, $topic]) {
            Lesson::create([
                'course_id'   => $c3->id,
                'title'       => $title,
                'description' => $desc,
                'topic'       => $topic,
                'level'       => 2,
                'order'       => 20 + $i + 1,
                'is_active'   => true,
                'order_in_course' => $i + 1,
            ]);
        }

        // Курс 4 — Эндшпиль
        $c4 = Course::create([
            'title'            => 'Эндшпиль: основы',
            'difficulty'       => 'intermediate',
            'required_level'   => 2,
            'duration_minutes' => 100,
            'is_new'           => false,
        ]);
        $lessons4 = [
            ['Король в эндшпиле',         'Почему в эндшпиле король становится активной фигурой.', 'endgame'],
            ['Пешечный эндшпиль',         'Правило квадрата, оппозиция, проходная пешка.', 'endgame'],
            ['Ладья против пешки',        'Как выиграть и как защититься в ладейном эндшпиле.', 'endgame'],
            ['Ферзь против ладьи',        'Техника реализации лишнего ферзя.', 'endgame'],
            ['Цугцванг',                  'Позиции, где любой ход проигрывает. Как создавать и избегать.', 'endgame'],
        ];
        foreach ($lessons4 as $i => [$title, $desc, $topic]) {
            Lesson::create([
                'course_id'   => $c4->id,
                'title'       => $title,
                'description' => $desc,
                'topic'       => $topic,
                'level'       => 2,
                'order'       => 30 + $i + 1,
                'is_active'   => true,
                'order_in_course' => $i + 1,
            ]);
        }

        // Курс 5 — Продвинутая тактика
        $c5 = Course::create([
            'title'            => 'Продвинутая тактика',
            'difficulty'       => 'advanced',
            'required_level'   => 3,
            'duration_minutes' => 150,
            'is_new'           => true,
        ]);
        $lessons5 = [
            ['Жертва фигуры',          'Когда отдавать материал ради атаки. Позиционные и тактические жертвы.', 'tactics'],
            ['Греческий подарок',      'Классическая жертва слона на h7/h2. Условия и варианты.', 'tactics'],
            ['Мельница',               'Серия вскрытых шахов, уничтожающих фигуры соперника.', 'tactics'],
            ['Вскрытый шах',           'Самый опасный тактический приём. Как создавать и защищаться.', 'tactics'],
            ['Мат в 2-3 хода',         'Систематическое решение многоходовых комбинаций.', 'tactics'],
            ['Позиционные жертвы',     'Жертвуем материал ради долгосрочного позиционного преимущества.', 'strategy'],
        ];
        foreach ($lessons5 as $i => [$title, $desc, $topic]) {
            Lesson::create([
                'course_id'   => $c5->id,
                'title'       => $title,
                'description' => $desc,
                'topic'       => $topic,
                'level'       => 3,
                'order'       => 40 + $i + 1,
                'is_active'   => true,
                'order_in_course' => $i + 1,
            ]);
        }

        // Курс 6 — Стратегия
        $c6 = Course::create([
            'title'            => 'Стратегия и планирование',
            'difficulty'       => 'advanced',
            'required_level'   => 4,
            'duration_minutes' => 180,
            'is_new'           => false,
        ]);
        $lessons6 = [
            ['Слабые поля',            'Как определить и использовать слабые поля в позиции соперника.', 'strategy'],
            ['Хорошие и плохие слоны', 'Почему один слон сильнее другого и как это использовать.', 'strategy'],
            ['Открытые линии',         'Захват открытых вертикалей ладьями. 7-я горизонталь.', 'strategy'],
            ['Пешечная структура',     'Изолированная пешка, сдвоенные пешки, проходная пешка.', 'strategy'],
            ['Миттельшпиль',           'Как составлять план в середине игры. Профилактика.', 'strategy'],
            ['Партии чемпионов',       'Анализ ключевых партий Каспарова, Карлсена, Фишера.', 'strategy'],
        ];
        foreach ($lessons6 as $i => [$title, $desc, $topic]) {
            Lesson::create([
                'course_id'   => $c6->id,
                'title'       => $title,
                'description' => $desc,
                'topic'       => $topic,
                'level'       => 4,
                'order'       => 50 + $i + 1,
                'is_active'   => true,
                'order_in_course' => $i + 1,
            ]);
        }
    }
}
