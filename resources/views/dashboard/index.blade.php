@extends('layouts.app')
@section('title', 'Дашборд')

@push('styles')
<style>
.db-wrap { max-width: 960px; margin: 0 auto; padding: 1.5rem 1rem; }

/* Greeting */
.db-greeting { font-size: 1.4rem; font-weight: 500; color: var(--color-text); margin-bottom: 4px; }
.db-sub { font-size: 0.85rem; color: var(--color-muted); margin-bottom: 1.5rem; }

/* Stats row */
.stats-row {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 10px;
    margin-bottom: 1.5rem;
}
.stat-card {
    background: var(--color-surface);
    border: 0.5px solid var(--color-border);
    border-radius: 10px;
    padding: 14px 16px;
}
.stat-label { font-size: 0.75rem; color: var(--color-muted); margin-bottom: 6px; display: block; }
.stat-value { font-size: 1.4rem; font-weight: 500; color: var(--color-text); display: block; }
.stat-delta { font-size: 0.72rem; color: var(--color-muted); margin-top: 3px; display: block; }
.stat-streak { color: #BA7517; }

/* Alert */
.alert {
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 0.85rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 10px;
}
.alert-info {
    background: rgba(79,70,229,0.07);
    border: 0.5px solid rgba(79,70,229,0.2);
    color: var(--color-text);
}
.alert-info a { color: #4f46e5; font-weight: 500; text-decoration: none; }
.alert-info a:hover { text-decoration: underline; }
.alert-icon { font-size: 1rem; flex-shrink: 0; }

/* Action cards */
.action-cards {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
    margin-bottom: 1.5rem;
}
.action-card {
    background: var(--color-surface);
    border: 0.5px solid var(--color-border);
    border-radius: 14px;
    padding: 18px 16px;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    gap: 4px;
    transition: border-color .15s, transform .15s;
}
.action-card:hover { border-color: var(--color-muted); transform: translateY(-2px); }
.action-icon {
    width: 32px; height: 32px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem;
    margin-bottom: 6px;
}
.action-icon.purple { background: rgba(83,74,183,0.12); color: #534AB7; }
.action-icon.teal   { background: rgba(15,110,86,0.12);  color: #0F6E56; }
.action-icon.amber  { background: rgba(186,117,23,0.12); color: #854F0B; }
[data-theme="dark"] .action-icon.purple { background: rgba(174,168,236,0.15); color: #AFA9EC; }
[data-theme="dark"] .action-icon.teal   { background: rgba(93,202,165,0.15);  color: #5DCAA5; }
[data-theme="dark"] .action-icon.amber  { background: rgba(239,159,39,0.15);  color: #EF9F27; }
.action-title { font-size: 0.88rem; font-weight: 500; color: var(--color-text); }
.action-desc  { font-size: 0.78rem; color: var(--color-muted); }

/* Tabs */
.tabs-header {
    display: flex;
    gap: 0;
    border-bottom: 0.5px solid var(--color-border);
    margin-bottom: 16px;
}
.tab-btn {
    padding: 8px 16px;
    font-size: 0.85rem;
    cursor: pointer;
    border: none;
    background: none;
    color: var(--color-muted);
    border-bottom: 2px solid transparent;
    margin-bottom: -0.5px;
    font-family: inherit;
    transition: color .15s;
}
.tab-btn.active { color: var(--color-text); border-bottom-color: var(--color-text); font-weight: 500; }
.tab-pane { display: none; }
.tab-pane.active { display: block; }

/* Course cards */
.courses-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 12px;
}
.course-card {
    background: var(--color-surface);
    border: 0.5px solid var(--color-border);
    border-radius: 14px;
    padding: 16px;
    text-decoration: none;
    display: block;
    transition: border-color .15s, transform .15s;
}
.course-card:hover { border-color: var(--color-muted); transform: translateY(-2px); }
.course-card.locked { opacity: 0.6; cursor: default; pointer-events: none; }
.course-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; gap: 6px; flex-wrap: wrap; }
.badge {
    font-size: 0.72rem; font-weight: 500;
    padding: 3px 10px; border-radius: 20px;
}
.badge-beginner  { background: rgba(29,158,117,0.1);  color: #0F6E56; }
.badge-intermediate { background: rgba(186,117,23,0.12); color: #854F0B; }
.badge-advanced  { background: rgba(153,60,29,0.1);   color: #993C1D; }
.badge-new       { background: rgba(83,74,183,0.1);   color: #534AB7; }
[data-theme="dark"] .badge-beginner     { background: rgba(93,202,165,0.15); color: #5DCAA5; }
[data-theme="dark"] .badge-intermediate { background: rgba(239,159,39,0.15); color: #EF9F27; }
[data-theme="dark"] .badge-advanced     { background: rgba(240,149,123,0.15); color: #F0977B; }
[data-theme="dark"] .badge-new          { background: rgba(174,168,236,0.15); color: #AFA9EC; }
.course-lock { font-size: 0.9rem; color: var(--color-muted); }
.course-name { font-size: 0.88rem; font-weight: 500; color: var(--color-text); margin-bottom: 3px; }
.course-meta { font-size: 0.75rem; color: var(--color-muted); margin-bottom: 10px; }
.course-progress { display: flex; align-items: center; gap: 8px; }
.progress-bar-bg { flex: 1; background: var(--color-border); border-radius: 4px; height: 5px; overflow: hidden; }
.progress-bar-fill { height: 100%; border-radius: 4px; background: #534AB7; transition: width .5s ease; }
.progress-bar-fill.done { background: #1D9E75; }
.progress-bar-fill.locked { background: #B4B2A9; }
.progress-label { font-size: 0.72rem; color: var(--color-muted); white-space: nowrap; }

/* Lessons list */
.lessons-list { display: flex; flex-direction: column; gap: 8px; }
.lesson-item {
    background: var(--color-surface);
    border: 0.5px solid var(--color-border);
    border-radius: 12px;
    padding: 14px 16px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: border-color .15s, transform .15s;
}
.lesson-item:hover { border-color: var(--color-muted); transform: translateY(-1px); }
.lesson-num {
    width: 32px; height: 32px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.78rem; font-weight: 500;
    flex-shrink: 0;
}
.ln-done { background: rgba(29,158,117,0.1); color: #0F6E56; }
.ln-active { background: rgba(83,74,183,0.1); color: #534AB7; }
.ln-locked { background: var(--color-border); color: var(--color-muted); }
.lesson-name { font-size: 0.88rem; font-weight: 500; color: var(--color-text); margin-bottom: 2px; }
.lesson-course { font-size: 0.75rem; color: var(--color-muted); }

/* Puzzle grid */
.puzzles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 12px;
}
.puzzle-card {
    background: var(--color-surface);
    border: 0.5px solid var(--color-border);
    border-radius: 14px;
    padding: 14px;
    text-decoration: none;
    display: block;
    transition: border-color .15s, transform .15s;
}
.puzzle-card:hover { border-color: var(--color-muted); transform: translateY(-2px); }
.puzzle-board {
    width: 100%; aspect-ratio: 1;
    background: #b58863;
    border-radius: 6px;
    margin-bottom: 10px;
    display: grid;
    grid-template-columns: repeat(8, 1fr);
    overflow: hidden;
}
.pb-cell { aspect-ratio: 1; }
.pb-cell.light { background: #f0d9b5; }
.pb-cell.dark  { background: #b58863; }
.pb-piece { display: flex; align-items: center; justify-content: center; font-size: 11px; color: #1a1a1a; }
.puzzle-name { font-size: 0.85rem; font-weight: 500; color: var(--color-text); margin-bottom: 4px; }
.puzzle-meta { font-size: 0.75rem; color: var(--color-muted); display: flex; align-items: center; gap: 5px; }
.diff-dot { width: 6px; height: 6px; border-radius: 50%; display: inline-block; flex-shrink: 0; }
.diff-easy { background: #1D9E75; }
.diff-medium { background: #BA7517; }
.diff-hard { background: #993C1D; }

/* Section title */
.section-title { font-size: 1rem; font-weight: 500; color: var(--color-text); margin-bottom: 14px; }

@media (max-width: 600px) {
    .stats-row { grid-template-columns: repeat(2, 1fr); }
    .action-cards { grid-template-columns: 1fr; }
    .courses-grid { grid-template-columns: 1fr; }
    .puzzles-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>
@endpush

@section('content')
<div class="db-wrap">

    {{-- Greeting --}}
    <div class="db-greeting">Привет, {{ auth()->user()->name }}!</div>
    <div class="db-sub">
        Последний визит: сегодня
        @if(auth()->user()->streak > 0)
            · Серия: {{ auth()->user()->streak }} {{ trans_choice('день|дня|дней', auth()->user()->streak) }} подряд 🔥
        @endif
    </div>

    {{-- Stats --}}
    <div class="stats-row">
        <div class="stat-card">
            <span class="stat-label">Рейтинг</span>
            <span class="stat-value">{{ auth()->user()->rating }}</span>
            <span class="stat-delta">
		    {{ $ratingGain >= 0 ? '+' : '' }}{{ $ratingGain }} за неделю
		</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Уровень</span>
            <span class="stat-value">{{ auth()->user()->level }} / 5</span>
            <span class="stat-delta">
                @php
                    $levels = ['','Новичок','Начинающий','Средний','Продвинутый','Мастер'];
                @endphp
                {{ $levels[auth()->user()->level] ?? '' }}
            </span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Уроков пройдено</span>
            <span class="stat-value">{{ $completed }}</span>
            <span class="stat-delta">из {{ $totalLessons ?? '—' }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Серия</span>
            <span class="stat-value stat-streak">{{ auth()->user()->streak ?? 0 }} 🔥</span>
            <span class="stat-delta">Лучшая: {{ auth()->user()->best_streak ?? 0 }}</span>
        </div>
    </div>

    {{-- Diagnostic nudge --}}
    @if(auth()->user()->level == 1 && $completed == 0)
        <div class="alert alert-info">
            <span class="alert-icon">♟</span>
            <span>
                Начни с <a href="/diagnostic">диагностики</a> — тренер определит твой уровень и подберёт курсы!
            </span>
        </div>
    @endif

    {{-- Action cards --}}
    <div class="action-cards">
        <a href="/trainer" class="action-card">
            <div class="action-icon purple">♟</div>
            <span class="action-title">Играть с тренером</span>
            <span class="action-desc">Партия с комментариями Гарри</span>
        </a>
        <a href="/puzzles" class="action-card">
            <div class="action-icon teal">⚔</div>
            <span class="action-title">Тактические задачи</span>
            <span class="action-desc">5 новых задач сегодня</span>
        </a>
        <a href="/progress" class="action-card">
            <div class="action-icon amber">◈</div>
            <span class="action-title">Мой прогресс</span>
            <span class="action-desc">Статистика и графики</span>
        </a>
    </div>

    {{-- Tabs: Courses / Lessons / Puzzles --}}
    <div class="section-title">Обучение</div>
    <div class="tabs-header">
        <button class="tab-btn active" onclick="switchTab('courses', this)">Курсы</button>
        <button class="tab-btn" onclick="switchTab('lessons', this)">Уроки</button>
        <button class="tab-btn" onclick="switchTab('puzzles', this)">Задачи</button>
    </div>

    {{-- Tab: Courses --}}
    <div id="tab-courses" class="tab-pane active">
        <div class="courses-grid">
            @forelse($courses as $course)
                @php
                    $progress = $course->lessons_count > 0
                        ? round($course->completed_count / $course->lessons_count * 100)
                        : 0;
                    $isLocked = auth()->user()->level < $course->required_level;
                    $isDone   = $progress === 100;
                    $badgeClass = match($course->difficulty) {
                        'beginner'     => 'badge-beginner',
                        'intermediate' => 'badge-intermediate',
                        'advanced'     => 'badge-advanced',
                        default        => 'badge-beginner',
                    };
                    $badgeLabel = match($course->difficulty) {
                        'beginner'     => 'Новичок',
                        'intermediate' => 'Средний',
                        'advanced'     => 'Продвинутый',
                        default        => 'Новичок',
                    };
                @endphp
                <a href="{{ $isLocked ? '#' : '/course/'.$course->id }}"
                   class="course-card {{ $isLocked ? 'locked' : '' }}">
                    <div class="course-top">
                        <span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                        @if($course->is_new && !$isLocked)
                            <span class="badge badge-new">Новый</span>
                        @endif
                        @if($isLocked)
                            <span class="course-lock">🔒</span>
                        @endif
                    </div>
                    <div class="course-name">{{ $course->title }}</div>
                    <div class="course-meta">
                        {{ $course->lessons_count }} {{ trans_choice('урок|урока|уроков', $course->lessons_count) }}
                        @if($course->duration_minutes)
                            · ~{{ round($course->duration_minutes / 60, 1) }} ч
                        @endif
                        @if($isLocked)
                            · Уровень {{ $course->required_level }}+
                        @endif
                    </div>
                    <div class="course-progress">
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill {{ $isDone ? 'done' : ($isLocked ? 'locked' : '') }}"
                                 style="width: {{ $isLocked ? 0 : $progress }}%"></div>
                        </div>
                        <span class="progress-label">
                            @if($isLocked)
                                заблокирован
                            @elseif($isDone)
                                ✓ пройден
                            @else
                                {{ $course->completed_count }}/{{ $course->lessons_count }}
                            @endif
                        </span>
                    </div>
                </a>
            @empty
                <p style="color: var(--color-muted); font-size: 0.88rem; grid-column: 1/-1;">
                    Курсы пока не добавлены.
                </p>
            @endforelse
        </div>
    </div>

    {{-- Tab: Lessons --}}
    <div id="tab-lessons" class="tab-pane">
        <div class="lessons-list">
            @forelse($lessons as $lesson)
                @php
                    $status = $lesson->pivot->status ?? 'locked';
                    // status: 'done' | 'active' | 'locked'
                    $numClass = match($status) {
                        'done'   => 'ln-done',
                        'active' => 'ln-active',
                        default  => 'ln-locked',
                    };
                    $numLabel = match($status) {
                        'done'   => '✓',
                        'active' => '→',
                        default  => $lesson->order_in_course ?? '·',
                    };
                @endphp
                <a href="{{ $status !== 'locked' ? '/lesson/'.$lesson->id : '#' }}"
                   class="lesson-item">
                    <div class="lesson-num {{ $numClass }}">{{ $numLabel }}</div>
                    <div>
                        <div class="lesson-name">{{ $lesson->title }}</div>
                        <div class="lesson-course">
                            {{ $lesson->course->title ?? '' }}
                            @if($lesson->order_in_course)
                                · Урок {{ $lesson->order_in_course }}
                            @endif
                            @if($status === 'active')
                                · <span style="color:#534AB7">В процессе</span>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <p style="color: var(--color-muted); font-size: 0.88rem;">Уроки пока не назначены.</p>
            @endforelse
        </div>
    </div>

    {{-- Tab: Puzzles --}}
    <div id="tab-puzzles" class="tab-pane">
        <div class="puzzles-grid">
            @forelse($puzzles as $puzzle)
                @php
                    $dotClass = match($puzzle->difficulty) {
                        'easy'   => 'diff-easy',
                        'medium' => 'diff-medium',
                        'hard'   => 'diff-hard',
                        default  => 'diff-easy',
                    };
                    $diffLabel = match($puzzle->difficulty) {
                        'easy'   => 'Лёгкая',
                        'medium' => 'Средняя',
                        'hard'   => 'Сложная',
                        default  => '',
                    };
                @endphp
                <a href="/puzzle/{{ $puzzle->id }}" class="puzzle-card">
                    {{-- Mini chess board (static placeholder, replace with real FEN renderer if needed) --}}
                    <div class="puzzle-board" id="puzzle-board-{{ $puzzle->id }}"></div>
                    <div class="puzzle-name">{{ $puzzle->title }}</div>
                    <div class="puzzle-meta">
                        <span class="diff-dot {{ $dotClass }}"></span>
                        {{ $diffLabel }}
                        @if($puzzle->rating_range)
                            · {{ $puzzle->rating_range }}
                        @endif
                    </div>
                </a>
            @empty
                <p style="color: var(--color-muted); font-size: 0.88rem; grid-column: 1/-1;">
                    Задачи пока не добавлены.
                </p>
            @endforelse
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function switchTab(name, btn) {
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    btn.classList.add('active');
}

/**
 * Renders a mini 8×8 board inside a .puzzle-board element.
 * pieces: object { "row,col": "♟" }
 */
function renderMiniBoard(id, pieces) {
    const el = document.getElementById(id);
    if (!el) return;
    for (let r = 0; r < 8; r++) {
        for (let c = 0; c < 8; c++) {
            const cell = document.createElement('div');
            cell.className = 'pb-cell ' + ((r + c) % 2 === 0 ? 'light' : 'dark');
            const key = r + ',' + c;
            if (pieces[key]) {
                const piece = document.createElement('div');
                piece.className = 'pb-piece';
                piece.textContent = pieces[key];
                cell.appendChild(piece);
            }
            el.appendChild(cell);
        }
    }
}

// Example static boards — replace with dynamic data from Blade if available.
// You can pass puzzle FEN data from PHP as a JSON variable:
//   const puzzleBoards = @json($puzzleBoards ?? []);
// and then call renderMiniBoard for each.

// Fallback: render empty boards for any .puzzle-board elements that were not filled.
// Передаём FEN из PHP в JS
const puzzleFens = @json($puzzles->pluck('fen', 'id'));

const PIECE_MAP = {
    'r':'♜','n':'♞','b':'♝','q':'♛','k':'♚','p':'♟',
    'R':'♖','N':'♘','B':'♗','Q':'♕','K':'♔','P':'♙',
};

function fenToBoard(fen) {
    const pieces = {};
    const rows = fen.split(' ')[0].split('/');
    rows.forEach((row, ri) => {
        let ci = 0;
        for (const ch of row) {
            if (ch >= '1' && ch <= '8') { ci += parseInt(ch); }
            else { pieces[ri + ',' + ci] = ch; ci++; }
        }
    });
    return pieces;
}

document.querySelectorAll('.puzzle-board').forEach(el => {
    const id = el.id.replace('puzzle-board-', '');
    const fen = puzzleFens[id];
    const pieces = fen ? fenToBoard(fen) : {};
    renderMiniBoard(el.id, pieces);
});
</script>
@endpush

