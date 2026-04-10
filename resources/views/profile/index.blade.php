@extends('layouts.app')
@section('title', 'Профиль')

@push('styles')
<style>
.profile-page { max-width: 900px; margin: 0 auto; padding: 2rem 1rem 3rem; }

.profile-hero {
    background: var(--color-surface); border: 0.5px solid var(--color-border);
    border-radius: 20px; padding: 2.5rem; margin-bottom: 1.5rem;
    display: flex; align-items: center; gap: 2rem;
    position: relative; overflow: hidden;
}
.profile-hero::before {
    content: '♟'; position: absolute; right: 2rem; top: 50%;
    transform: translateY(-50%); font-size: 8rem; opacity: 0.04; line-height: 1;
    color: var(--color-text);
}
.avatar-circle {
    width: 88px; height: 88px; border-radius: 50%; flex-shrink: 0; background: #4f46e5;
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem; font-weight: 500; color: white;
}
.profile-hero-info h1 { font-size: 1.4rem; font-weight: 500; color: var(--color-text); margin: 0 0 4px; }
.profile-hero-info p  { color: var(--color-muted); font-size: 0.85rem; margin: 0 0 1rem; }
.level-badges { display: flex; gap: 0.5rem; flex-wrap: wrap; }
.badge { padding: 4px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 500; }
.badge-level  { background: #4f46e5; color: white; }
.badge-rating { background: var(--color-bg); color: var(--color-muted); border: 0.5px solid var(--color-border); }
.badge-rank   { background: rgba(56,161,105,0.12); color: #38a169; }
[data-theme="dark"] .badge-rank { color: #68d391; }

.stats-grid {
    display: grid; grid-template-columns: repeat(4, 1fr);
    gap: 12px; margin-bottom: 1.5rem;
}
.stat-card {
    background: var(--color-surface); border: 0.5px solid var(--color-border);
    border-radius: 14px; padding: 1.25rem; text-align: center;
}
.stat-icon { font-size: 1.4rem; margin-bottom: 0.5rem; }
.stat-num  { font-size: 1.6rem; font-weight: 500; color: var(--color-text); line-height: 1; }
.stat-lbl  { font-size: 0.76rem; color: var(--color-muted); margin-top: 4px; }

.two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }

.card { background: var(--color-surface); border: 0.5px solid var(--color-border); border-radius: 16px; padding: 1.5rem; }
.card-title {
    font-size: 0.72rem; font-weight: 500; letter-spacing: 0.06em;
    text-transform: uppercase; color: var(--color-muted); margin-bottom: 1.25rem;
}

/* ── Путь развития ── */
.level-row {
    display: flex; align-items: center; gap: 0.75rem;
    padding: 0.6rem 0; border-bottom: 0.5px solid var(--color-border); text-decoration: none;
}
.level-row:last-child { border-bottom: none; }
a.level-row { border-radius: 8px; padding-left: 6px; margin-left: -6px; transition: background .15s, padding-left .15s; }
a.level-row:hover { background: var(--color-bg); padding-left: 10px; }
.level-row-locked { opacity: 0.45; pointer-events: none; }
.level-num {
    width: 26px; height: 26px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.76rem; font-weight: 500; flex-shrink: 0;
}
.lv-done    { background: #4f46e5; color: white; }
.lv-current { background: rgba(240,192,64,0.15); color: #b8922e; border: 1.5px solid #f0c040; }
[data-theme="dark"] .lv-current { color: #f0c040; }
.lv-future  { background: var(--color-bg); color: var(--color-muted); }
.level-name { font-size: 0.875rem; flex: 1; color: var(--color-text); }
.level-check { color: #4f46e5; font-size: 0.875rem; }
.level-current-label { font-size: 0.72rem; color: var(--color-muted); }

/* ── Диагностика ── */
.diagnostic-box {
    background: var(--color-bg); border: 0.5px solid var(--color-border);
    border-radius: 10px; padding: 1rem; margin-bottom: 1rem;
}
.diagnostic-box p { font-size: 0.875rem; color: var(--color-text); margin: 0; line-height: 1.6; }
.no-diagnostic { color: var(--color-muted); font-size: 0.875rem; }
.diag-link {
    display: block; text-align: center; padding: 0.7rem; background: var(--color-bg); color: #4f46e5;
    border: 0.5px solid var(--color-border); border-radius: 10px; font-size: 0.875rem;
    font-weight: 500; text-decoration: none; margin-top: 0.75rem; transition: border-color 0.15s;
}
.diag-link:hover { border-color: #4f46e5; }

/* ── 🏅 Достижения ── */
.achievements-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 10px; }
.ach-item {
    display: flex; flex-direction: column; align-items: center; gap: 6px;
    padding: 14px 8px; border-radius: 12px; border: 0.5px solid var(--color-border);
    background: var(--color-bg); text-align: center; transition: border-color .15s, transform .15s;
}
.ach-item.unlocked { border-color: rgba(79,70,229,0.35); background: rgba(79,70,229,0.04); }
.ach-item.unlocked:hover { transform: translateY(-2px); border-color: rgba(79,70,229,0.6); }
.ach-item.locked   { opacity: 0.35; filter: grayscale(1); }
.ach-icon { font-size: 1.7rem; line-height: 1; }
.ach-name { font-size: 0.68rem; font-weight: 600; color: var(--color-text); line-height: 1.3; }
.ach-desc { font-size: 0.62rem; color: var(--color-muted); line-height: 1.3; }
.ach-progress { font-size: 0.6rem; color: var(--color-muted); margin-top: 2px; }

/* ── 📊 Тепловая карта ── */
.heatmap-wrap { overflow-x: auto; padding-bottom: 4px; }
.heatmap-grid { display: flex; gap: 3px; }
.heatmap-col  { display: flex; flex-direction: column; gap: 3px; }
.hm-cell {
    width: 13px; height: 13px; border-radius: 2px;
    background: var(--color-border); cursor: default; flex-shrink: 0;
}
.hm-cell:hover { opacity: .7; }
.hm-1 { background: rgba(79,70,229,0.22); }
.hm-2 { background: rgba(79,70,229,0.45); }
.hm-3 { background: rgba(79,70,229,0.70); }
.hm-4 { background: rgba(79,70,229,1.00); }
.heatmap-legend {
    display: flex; align-items: center; gap: 4px; margin-top: 8px;
    justify-content: flex-end; font-size: 0.68rem; color: var(--color-muted);
}
.hm-leg { width: 11px; height: 11px; border-radius: 2px; flex-shrink: 0; }

/* ── 🎯 Темы ── */
.topic-bars { display: flex; flex-direction: column; gap: 12px; }
.topic-row  { display: flex; flex-direction: column; gap: 4px; }
.topic-meta { display: flex; justify-content: space-between; align-items: center; font-size: 0.78rem; }
.topic-label { color: var(--color-text); font-weight: 500; display: flex; align-items: center; gap: 5px; }
.topic-count { color: var(--color-muted); }
.topic-bar-bg   { height: 6px; background: var(--color-border); border-radius: 3px; overflow: hidden; }
.topic-bar-fill { height: 100%; border-radius: 3px; background: #4f46e5; transition: width .6s ease; }
.topic-fav-badge { font-size: 0.62rem; padding: 1px 7px; border-radius: 20px; background: rgba(79,70,229,0.12); color: #4f46e5; }

/* ── ⚙️ Настройки профиля ── */
.settings-tabs { display: flex; gap: 4px; margin-bottom: 1.25rem; border-bottom: 0.5px solid var(--color-border); padding-bottom: 0; }
.stab {
    padding: 7px 14px; font-size: 0.8rem; font-weight: 500; cursor: pointer;
    border: none; background: none; color: var(--color-muted); font-family: inherit;
    border-bottom: 2px solid transparent; margin-bottom: -1px; transition: color .15s;
}
.stab.active { color: #4f46e5; border-bottom-color: #4f46e5; }
.stab:hover:not(.active) { color: var(--color-text); }

.tab-panel { display: none; }
.tab-panel.active { display: block; }

.form-group { margin-bottom: 1rem; }
.form-label { font-size: 0.8rem; font-weight: 500; color: var(--color-muted); display: block; margin-bottom: 6px; }
.form-input {
    width: 100%; padding: 0.625rem 0.875rem; border: 0.5px solid var(--color-border);
    border-radius: 10px; font-size: 0.875rem; outline: none;
    background: var(--color-bg); color: var(--color-text);
    font-family: inherit; transition: border-color 0.15s; box-sizing: border-box;
}
.form-input:focus { border-color: #4f46e5; }
.form-input:disabled { opacity: 0.5; cursor: not-allowed; }
textarea.form-input { resize: vertical; min-height: 80px; line-height: 1.5; }
.form-hint { font-size: 0.72rem; color: var(--color-muted); margin-top: 4px; }
.char-count { font-size: 0.72rem; color: var(--color-muted); text-align: right; margin-top: 2px; }

.save-btn {
    width: 100%; padding: 0.7rem; border-radius: 10px; background: #4f46e5; color: white;
    border: none; font-size: 0.875rem; font-weight: 500; cursor: pointer;
    font-family: inherit; transition: background 0.15s;
}
.save-btn:hover { background: #4338ca; }

/* Стили фигур — превью */
.piece-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-bottom: 1rem; }
.piece-opt {
    border: 1.5px solid var(--color-border); border-radius: 10px; padding: 10px 6px;
    text-align: center; cursor: pointer; transition: border-color .15s, background .15s;
    background: var(--color-bg);
}
.piece-opt:hover   { border-color: var(--color-muted); }
.piece-opt.selected { border-color: #4f46e5; background: rgba(79,70,229,0.06); }
.piece-opt input   { display: none; }
.piece-glyph { font-size: 1.6rem; line-height: 1; margin-bottom: 4px; display: block; }
.piece-name  { font-size: 0.65rem; color: var(--color-muted); }

/* Смена пароля */
.pass-strength { height: 4px; border-radius: 2px; margin-top: 6px; background: var(--color-border); overflow: hidden; }
.pass-strength-fill { height: 100%; border-radius: 2px; transition: width .3s, background .3s; width: 0; }

.danger-zone {
    border: 0.5px solid rgba(163,45,45,0.25); border-radius: 12px;
    padding: 1rem 1.25rem; background: rgba(163,45,45,0.03);
}
.danger-title { font-size: 0.78rem; font-weight: 600; color: #a32d2d; margin-bottom: 6px; }
.danger-desc  { font-size: 0.8rem; color: var(--color-muted); margin-bottom: 12px; }
.danger-btn {
    padding: 0.55rem 1.2rem; border-radius: 8px; background: transparent; color: #a32d2d;
    border: 0.5px solid rgba(163,45,45,0.35); font-size: 0.82rem; font-weight: 500;
    cursor: pointer; font-family: inherit; transition: background .15s;
}
.danger-btn:hover { background: rgba(163,45,45,0.08); }

.success-msg {
    background: rgba(56,161,105,0.1); color: #38a169; padding: 0.75rem 1rem;
    border-radius: 10px; font-size: 0.875rem; margin-bottom: 1.25rem;
    border: 0.5px solid rgba(56,161,105,0.25);
}
.error-msg {
    background: rgba(163,45,45,0.08); color: #a32d2d; padding: 0.75rem 1rem;
    border-radius: 10px; font-size: 0.875rem; margin-bottom: 1rem;
    border: 0.5px solid rgba(163,45,45,0.2);
}

/* Быстрые действия */
.quick-link {
    display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem;
    background: var(--color-bg); border: 0.5px solid var(--color-border); border-radius: 10px;
    text-decoration: none; color: var(--color-text); font-size: 0.875rem; transition: border-color 0.15s;
}
.quick-link:hover { border-color: var(--color-muted); }
.quick-link-icon { font-size: 1.2rem; }
.quick-links { display: flex; flex-direction: column; gap: 0.5rem; }
.logout-btn {
    width: 100%; padding: 0.7rem; border-radius: 10px;
    background: rgba(163,45,45,0.08); color: #a32d2d;
    border: 0.5px solid rgba(163,45,45,0.2); font-size: 0.875rem; font-weight: 500;
    cursor: pointer; font-family: inherit; margin-top: 1.25rem; transition: background 0.15s;
}
.logout-btn:hover { background: rgba(163,45,45,0.15); }

@media(max-width: 700px) {
    .stats-grid        { grid-template-columns: repeat(2, 1fr); }
    .two-col           { grid-template-columns: 1fr; }
    .profile-hero      { flex-direction: column; text-align: center; }
    .achievements-grid { grid-template-columns: repeat(3, 1fr); }
    .piece-grid        { grid-template-columns: repeat(3, 1fr); }
}
</style>
@endpush

@section('content')
<div class="profile-page">

    {{-- Hero --}}
    <div class="profile-hero">
        <div class="avatar-circle">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
        <div class="profile-hero-info">
            <h1>{{ $user->name }}</h1>
            <p>{{ $user->email }} · Участник с {{ $user->created_at->format('d.m.Y') }}</p>
            @if($user->bio)
                <p style="margin-top:4px;font-size:0.83rem;color:var(--color-text);opacity:0.8">{{ $user->bio }}</p>
            @endif
            <div class="level-badges" style="margin-top:{{ $user->bio ? '8px' : '0' }}">
                <span class="badge badge-level">Уровень {{ $user->level }}/5</span>
                <span class="badge badge-rating">♟ {{ $user->rating }}</span>
                <span class="badge badge-rank">
                    @php $ranks = [1=>'Новичок',2=>'Любитель',3=>'Средний',4=>'Продвинутый',5=>'Эксперт']; @endphp
                    {{ $ranks[$user->level] }}
                </span>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-card"><div class="stat-icon">🎮</div><div class="stat-num">{{ $games }}</div><div class="stat-lbl">Партий сыграно</div></div>
        <div class="stat-card"><div class="stat-icon">📚</div><div class="stat-num">{{ $completed }}</div><div class="stat-lbl">Уроков пройдено</div></div>
        <div class="stat-card"><div class="stat-icon">⭐</div><div class="stat-num">{{ $user->rating }}</div><div class="stat-lbl">Рейтинг</div></div>
        <div class="stat-card"><div class="stat-icon">🏆</div><div class="stat-num">{{ $winGames }}</div><div class="stat-lbl">Завершённых партий</div></div>
    </div>

    {{-- 🏅 Достижения --}}
    <div class="card" style="margin-bottom:1.25rem">
        <div class="card-title">🏅 Достижения</div>
        @php
            $puzzlesSolved = $user->solvedPuzzles()->count();
            $achievements  = [
                ['icon'=>'🔥','name'=>'Добро пожаловать','desc'=>'Зарегистрироваться',   'done'=>true,                 'prog'=>null],
                ['icon'=>'🎓','name'=>'Первый шаг',      'desc'=>'Пройти 1 урок',         'done'=>$completed>=1,        'prog'=>$completed.'/1'],
                ['icon'=>'📚','name'=>'Студент',          'desc'=>'Пройти 5 уроков',       'done'=>$completed>=5,        'prog'=>min($completed,5).'/5'],
                ['icon'=>'🎯','name'=>'Первая задача',    'desc'=>'Решить 1 задачу',        'done'=>$puzzlesSolved>=1,    'prog'=>$puzzlesSolved.'/1'],
                ['icon'=>'⚡','name'=>'Решатель',         'desc'=>'Решить 10 задач',        'done'=>$puzzlesSolved>=10,   'prog'=>min($puzzlesSolved,10).'/10'],
                ['icon'=>'🧩','name'=>'Мастер задач',     'desc'=>'Решить 25 задач',        'done'=>$puzzlesSolved>=25,   'prog'=>min($puzzlesSolved,25).'/25'],
                ['icon'=>'🎮','name'=>'Игрок',            'desc'=>'Сыграть 1 партию',       'done'=>$games>=1,           'prog'=>$games.'/1'],
                ['icon'=>'⭐','name'=>'Рейтинг 1100',     'desc'=>'Набрать 1100 очков',     'done'=>$user->rating>=1100, 'prog'=>$user->rating.'/1100'],
                ['icon'=>'🚀','name'=>'Рейтинг 1200',     'desc'=>'Набрать 1200 очков',     'done'=>$user->rating>=1200, 'prog'=>$user->rating.'/1200'],
                ['icon'=>'🏅','name'=>'Средний',          'desc'=>'Достичь уровня 3',       'done'=>$user->level>=3,     'prog'=>$user->level.'/3'],
                ['icon'=>'💎','name'=>'Продвинутый',      'desc'=>'Достичь уровня 4',       'done'=>$user->level>=4,     'prog'=>$user->level.'/4'],
                ['icon'=>'👑','name'=>'Эксперт',          'desc'=>'Достичь уровня 5',       'done'=>$user->level>=5,     'prog'=>$user->level.'/5'],
            ];
            $unlockedCount = collect($achievements)->where('done', true)->count();
        @endphp
        <div style="font-size:0.78rem;color:var(--color-muted);margin-bottom:14px">
            Открыто <strong style="color:var(--color-text)">{{ $unlockedCount }}</strong> из {{ count($achievements) }}
        </div>
        <div class="achievements-grid">
            @foreach($achievements as $a)
                <div class="ach-item {{ $a['done'] ? 'unlocked' : 'locked' }}" title="{{ $a['desc'] }}">
                    <div class="ach-icon">{{ $a['icon'] }}</div>
                    <div class="ach-name">{{ $a['name'] }}</div>
                    <div class="ach-desc">{{ $a['desc'] }}</div>
                    @if(!$a['done'] && $a['prog'])
                        <div class="ach-progress">{{ $a['prog'] }}</div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- 📊 Тепловая карта + 🎯 Темы --}}
    <div class="two-col" style="margin-bottom:1.25rem">
        <div class="card">
            <div class="card-title">📊 Активность за 18 недель</div>
            @php
                $lessonDates = $user->userLessons()->where('status','completed')->whereNotNull('completed_at')
                    ->pluck('completed_at')->map(fn($d) => \Carbon\Carbon::parse($d)->toDateString());
                $puzzleDates = $user->solvedPuzzles()->get()
                    ->map(fn($p) => \Carbon\Carbon::parse($p->pivot->created_at)->toDateString());
                $activityByDay = $lessonDates->merge($puzzleDates)->countBy()->toArray();
                $startDate = \Carbon\Carbon::today()->subWeeks(18)->startOfWeek(\Carbon\Carbon::MONDAY);
                $today = \Carbon\Carbon::today();
                $weeks = [];
                $d = $startDate->copy();
                while ($d->lte($today)) {
                    $week = $d->weekOfYear.'-'.$d->year;
                    $weeks[$week][] = $d->toDateString();
                    $d->addDay();
                }
            @endphp
            <div class="heatmap-wrap">
                <div class="heatmap-grid">
                    @foreach($weeks as $weekDays)
                        <div class="heatmap-col">
                            @foreach($weekDays as $day)
                                @php
                                    $cnt   = $activityByDay[$day] ?? 0;
                                    $level = $cnt===0 ? 0 : ($cnt===1 ? 1 : ($cnt<=3 ? 2 : ($cnt<=5 ? 3 : 4)));
                                @endphp
                                <div class="hm-cell {{ $level>0 ? 'hm-'.$level : '' }}"
                                     title="{{ $day }}{{ $cnt>0 ? ': '.$cnt.' действий' : '' }}"></div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="heatmap-legend">
                <span>Меньше</span>
                <div class="hm-leg" style="background:var(--color-border)"></div>
                <div class="hm-leg hm-1"></div><div class="hm-leg hm-2"></div>
                <div class="hm-leg hm-3"></div><div class="hm-leg hm-4"></div>
                <span>Больше</span>
            </div>
        </div>

        <div class="card">
            <div class="card-title">🎯 Темы уроков</div>
            @php
                $topicLabels = ['basics'=>'♟ Основы','tactics'=>'⚔ Тактика','opening'=>'🏰 Дебют','endgame'=>'👑 Эндшпиль','strategy'=>'🧠 Стратегия'];
                $topicStats  = $user->userLessons()->where('status','completed')->with('lesson')->get()
                    ->groupBy(fn($ul) => $ul->lesson->topic ?? 'basics')->map(fn($g) => $g->count())->sortDesc();
                $maxCount = $topicStats->max() ?: 1;
                $favTopic = $topicStats->keys()->first();
            @endphp
            @if($topicStats->isEmpty())
                <p style="font-size:0.85rem;color:var(--color-muted)">Пройди первые уроки — здесь появится статистика.</p>
            @else
                <div class="topic-bars">
                    @foreach($topicLabels as $key => $label)
                        @php $cnt = $topicStats[$key] ?? 0; @endphp
                        <div class="topic-row">
                            <div class="topic-meta">
                                <span class="topic-label">
                                    {{ $label }}
                                    @if($key===$favTopic && $cnt>0)<span class="topic-fav-badge">★ любимая</span>@endif
                                </span>
                                <span class="topic-count">{{ $cnt }} ур.</span>
                            </div>
                            <div class="topic-bar-bg">
                                <div class="topic-bar-fill" style="width:{{ $maxCount>0 ? round($cnt/$maxCount*100) : 0 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Нижняя сетка --}}
    <div class="two-col">
        <div>
            {{-- Путь развития --}}
            <div class="card" style="margin-bottom:1.25rem">
                <div class="card-title">Путь развития</div>
                @php
                    $levels = [1=>'Новичок — правила и ходы',2=>'Любитель — базовая тактика',3=>'Средний — комбинации',4=>'Продвинутый — стратегия',5=>'Эксперт — эндшпиль'];
                @endphp
                @foreach($levels as $lvl => $name)
                    @php $course = $coursesByLevel[$lvl] ?? null; $isLocked = $lvl > $user->level; @endphp
                    @if($course && !$isLocked)<a href="/course/{{ $course->id }}" class="level-row">
                    @else<div class="level-row {{ $isLocked ? 'level-row-locked' : '' }}">@endif
                        <div class="level-num {{ $lvl < $user->level ? 'lv-done' : ($lvl==$user->level ? 'lv-current' : 'lv-future') }}">{{ $lvl }}</div>
                        <span class="level-name">{{ $name }}</span>
                        @if($lvl < $user->level)<span class="level-check">✓</span>
                        @elseif($lvl==$user->level)<span class="level-current-label">← сейчас</span>
                        @else<span class="level-current-label">🔒</span>@endif
                    @if($course && !$isLocked)</a>@else</div>@endif
                @endforeach
            </div>

            {{-- Диагностика --}}
            <div class="card">
                <div class="card-title">Последняя диагностика</div>
                @if($diagnostic)
                    <div class="diagnostic-box">
                        <p>Пройдена {{ $diagnostic->created_at->format('d.m.Y') }}<br>
                        Результат: <strong>{{ $diagnostic->score }} очков</strong><br>
                        Уровень: <strong>{{ $diagnostic->level_assigned }}/5</strong></p>
                    </div>
                @else
                    <p class="no-diagnostic">Диагностика ещё не пройдена</p>
                @endif
                <a href="/diagnostic" class="diag-link">{{ $diagnostic ? '🔄 Пройти снова' : '▶ Пройти диагностику' }}</a>
            </div>
        </div>

        <div>
            {{-- ⚙️ Настройки профиля --}}
            <div class="card" style="margin-bottom:1.25rem">
                <div class="card-title">⚙️ Настройки профиля</div>

                @if(session('success'))
                    <div class="success-msg">✓ {{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="error-msg">{{ $errors->first() }}</div>
                @endif

                {{-- Вкладки --}}
                <div class="settings-tabs">
                    <button class="stab active" onclick="switchTab('general', this)">Основное</button>
                    <button class="stab" onclick="switchTab('appearance', this)">Внешний вид</button>
                    <button class="stab" onclick="switchTab('security', this)">Безопасность</button>
                </div>

                {{-- Вкладка: Основное --}}
                <div id="tab-general" class="tab-panel active">
                    <form method="POST" action="{{ route('profile.update') }}">
				    @csrf @method('PUT')                      
				      <div class="form-group">
                            <label class="form-label">Имя</label>
                            <input type="text" name="name" class="form-input" value="{{ old('name', $user->name) }}" required maxlength="255">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-input" value="{{ $user->email }}" disabled>
                            <div class="form-hint">Email нельзя изменить</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">О себе</label>
                            <textarea name="bio" class="form-input" maxlength="300" id="bio-input" oninput="updateCharCount(this)">{{ old('bio', $user->bio) }}</textarea>
                            <div class="char-count"><span id="bio-count">{{ strlen($user->bio ?? '') }}</span>/300</div>
                        </div>
                        <button type="submit" class="save-btn">Сохранить</button>
                    </form>
                </div>

                {{-- Вкладка: Внешний вид --}}
                <div id="tab-appearance" class="tab-panel">
                    <form method="POST" action="{{ route('profile.style') }}">
   				 @csrf @method('PUT')
                        <div class="form-group">
                            <label class="form-label">Стиль фигур</label>
                            <div class="piece-grid">
                                @php
                                    $pieces = [
                                        'cburnett'   => ['name'=>'Cburnett',   'glyph'=>'♛'],
                                        'merida'     => ['name'=>'Merida',     'glyph'=>'♜'],
                                        'alpha'      => ['name'=>'Alpha',      'glyph'=>'♝'],
                                        'pirouetti'  => ['name'=>'Pirouetti',  'glyph'=>'♞'],
                                        'chessnut'   => ['name'=>'Chessnut',   'glyph'=>'♟'],
                                        'chess7'     => ['name'=>'Chess7',     'glyph'=>'♚'],
                                        'reillycraig'=> ['name'=>'Reilly',     'glyph'=>'♛'],
                                        'companion'  => ['name'=>'Companion',  'glyph'=>'♜'],
                                    ];
                                    $currentStyle = $user->piece_style ?? 'cburnett';
                                @endphp
                                @foreach($pieces as $val => $p)
                                    <label class="piece-opt {{ $currentStyle === $val ? 'selected' : '' }}"
                                           onclick="selectPiece(this)">
                                        <input type="radio" name="piece_style" value="{{ $val }}"
                                               {{ $currentStyle === $val ? 'checked' : '' }}>
                                        <span class="piece-glyph">{{ $p['glyph'] }}</span>
                                        <div class="piece-name">{{ $p['name'] }}</div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <button type="submit" class="save-btn">Сохранить стиль</button>
                    </form>
                </div>

                {{-- Вкладка: Безопасность --}}
                <div id="tab-security" class="tab-panel">
	             <form method="POST" action="{{ route('profile.password') }}">
	    			@csrf @method('PUT')
                        <div class="form-group">
                            <label class="form-label">Текущий пароль</label>
                            <input type="password" name="current_password" class="form-input" placeholder="••••••••">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Новый пароль</label>
                            <input type="password" name="new_password" class="form-input"
                                   placeholder="Минимум 8 символов" oninput="checkStrength(this.value)">
                            <div class="pass-strength"><div class="pass-strength-fill" id="pass-fill"></div></div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Повторите новый пароль</label>
                            <input type="password" name="new_password_confirmation" class="form-input" placeholder="••••••••">
                        </div>
                        <button type="submit" class="save-btn">Сменить пароль</button>
                    </form>

                    <div style="margin-top:1.5rem">
                        <div class="danger-zone">
                            <div class="danger-title">⚠ Опасная зона</div>
                            <div class="danger-desc">Удаление аккаунта необратимо. Все данные будут потеряны.</div>
                            <form method="POST" action="{{ route('profile.destroy') }}" id="delete-form">
					    @csrf @method('DELETE')
					    <button type="button" class="danger-btn" onclick="confirmDelete()">Удалить аккаунт</button>
					</form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Быстрые действия --}}
            <div class="card">
                <div class="card-title">Быстрые действия</div>
                <div class="quick-links">
                    <a href="/trainer/setup" class="quick-link"><span class="quick-link-icon">♟</span>Играть с тренером</a>
                    <a href="/diagnostic"    class="quick-link"><span class="quick-link-icon">📋</span>Пройти диагностику</a>
                    <a href="/progress"      class="quick-link"><span class="quick-link-icon">📈</span>Мой прогресс</a>
                    <a href="/dashboard"     class="quick-link"><span class="quick-link-icon">🏠</span>На дашборд</a>
                </div>
                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="logout-btn">Выйти из аккаунта</button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// Вкладки
function switchTab(name, btn) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.stab').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    btn.classList.add('active');
}

// Запомнить активную вкладку если была ошибка
@php $tab = session('tab', 'general'); @endphp
const savedTab = '{{ $tab }}';
const tabBtn = document.querySelector(`.stab[onclick*="${savedTab}"]`);
if (tabBtn) switchTab(savedTab, tabBtn);

// Счётчик символов в "О себе"
function updateCharCount(el) {
    document.getElementById('bio-count').textContent = el.value.length;
}

// Выбор стиля фигур
function selectPiece(label) {
    document.querySelectorAll('.piece-opt').forEach(l => l.classList.remove('selected'));
    label.classList.add('selected');
}

// Сила пароля
function checkStrength(val) {
    const fill = document.getElementById('pass-fill');
    let score = 0;
    if (val.length >= 8)  score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const pct   = score * 25;
    const color = score <= 1 ? '#e05a5a' : score === 2 ? '#e8b84b' : score === 3 ? '#5a7fe8' : '#3dba82';
    fill.style.width = pct + '%';
    fill.style.background = color;
}

// Подтверждение удаления

function confirmDelete() {
    if (confirm('Вы уверены? Это действие необратимо — все ваши данные будут удалены.')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@endpush