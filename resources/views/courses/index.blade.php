@extends('layouts.app')
@section('title', 'Курсы')

@push('styles')
<style>
.cr-wrap { max-width: 960px; margin: 0 auto; padding: 1.5rem 1rem 3rem; }
.cr-header { margin-bottom: 1.6rem; }
.cr-title { font-size: 1.2rem; font-weight: 600; color: var(--color-text); margin-bottom: 4px; }
.cr-sub { font-size: 0.82rem; color: var(--color-muted); }

.section-label {
    font-size: 0.72rem; text-transform: uppercase; letter-spacing: .07em;
    color: var(--color-muted); font-weight: 500; margin-bottom: 12px; margin-top: 24px;
}

.courses-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 14px;
}

.course-card {
    background: var(--color-surface);
    border: 0.5px solid var(--color-border);
    border-radius: 16px;
    padding: 20px;
    text-decoration: none;
    display: block;
    transition: border-color .15s, transform .15s, box-shadow .15s;
    position: relative;
    overflow: hidden;
}
.course-card::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0; height: 3px;
    border-radius: 16px 16px 0 0;
}
.course-card.beginner::before   { background: linear-gradient(90deg, #3dba82, #5dcaa5); }
.course-card.intermediate::before { background: linear-gradient(90deg, #e8b84b, #f0c040); }
.course-card.advanced::before   { background: linear-gradient(90deg, #e05a5a, #e87a5a); }
.course-card:hover:not(.locked) { border-color: var(--color-muted); transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.12); }
.course-card.locked { opacity: 0.6; cursor: not-allowed; }

.course-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
.badge { font-size: 0.7rem; font-weight: 500; padding: 3px 10px; border-radius: 20px; }
.badge-beginner     { background: rgba(61,186,130,0.12); color: #3dba82; }
.badge-intermediate { background: rgba(232,184,75,0.12);  color: #c9951c; }
.badge-advanced     { background: rgba(224,90,90,0.12);   color: #e05a5a; }
.badge-new { background: rgba(90,127,232,0.12); color: #5a7fe8; margin-left: 6px; }

.course-icon {
    width: 40px; height: 40px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; margin-bottom: 10px;
}
.ci-green  { background: rgba(61,186,130,0.12); }
.ci-yellow { background: rgba(232,184,75,0.12); }
.ci-red    { background: rgba(224,90,90,0.12); }

.course-name { font-size: 0.95rem; font-weight: 600; color: var(--color-text); margin-bottom: 4px; }
.course-meta { font-size: 0.78rem; color: var(--color-muted); margin-bottom: 14px; }

.progress-row { display: flex; align-items: center; gap: 10px; }
.progress-bg { flex: 1; height: 5px; background: var(--color-border); border-radius: 3px; overflow: hidden; }
.progress-fill { height: 100%; border-radius: 3px; transition: width .5s ease; }
.pf-green  { background: #3dba82; }
.pf-yellow { background: #e8b84b; }
.pf-red    { background: #e05a5a; }
.pf-grey   { background: var(--color-muted); }
.progress-label { font-size: 0.72rem; color: var(--color-muted); white-space: nowrap; }

.lock-icon { font-size: 1rem; color: var(--color-muted); }
.level-req { font-size: 0.72rem; color: var(--color-muted); margin-top: 8px; }

@media (max-width: 600px) { .courses-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="cr-wrap">
    <div class="cr-header">
        <div class="cr-title">📚 Курсы</div>
        <div class="cr-sub">Выбери курс и начни обучение</div>
    </div>

    @foreach([
        ['beginner',     'Новичок',      $courses->where('difficulty','beginner')],
        ['intermediate', 'Средний',      $courses->where('difficulty','intermediate')],
        ['advanced',     'Продвинутый',  $courses->where('difficulty','advanced')],
    ] as [$diff, $label, $group])
        @if($group->count())
        <div class="section-label">{{ $label }}</div>
        <div class="courses-grid">
            @foreach($group as $course)
                @php
                    $progress = $course->lessons_count > 0
                        ? round($course->completed_count / $course->lessons_count * 100) : 0;
                    $isDone   = $progress === 100;
                    $icons    = ['basics'=>'♟','opening'=>'🏰','tactics'=>'⚔','endgame'=>'👑','strategy'=>'🧠'];
                    $ciClass  = ['beginner'=>'ci-green','intermediate'=>'ci-yellow','advanced'=>'ci-red'][$diff] ?? 'ci-green';
                    $pfClass  = ['beginner'=>'pf-green','intermediate'=>'pf-yellow','advanced'=>'pf-red'][$diff] ?? 'pf-green';
                    $badgeClass = 'badge-' . $diff;
                @endphp
                <a href="{{ $course->is_locked ? '#' : '/course/'.$course->id }}"
                   class="course-card {{ $diff }} {{ $course->is_locked ? 'locked' : '' }}">

                    <div class="course-top">
                        <div>
                            <span class="badge {{ $badgeClass }}">{{ $label }}</span>
                            @if($course->is_new && !$course->is_locked)
                                <span class="badge badge-new">Новый</span>
                            @endif
                        </div>
                        @if($course->is_locked)
                            <span class="lock-icon">🔒</span>
                        @elseif($isDone)
                            <span style="color:#3dba82;font-size:1rem">✓</span>
                        @endif
                    </div>

                    <div class="course-icon {{ $ciClass }}">♟</div>
                    <div class="course-name">{{ $course->title }}</div>
                    <div class="course-meta">
                        {{ $course->lessons_count }} {{ trans_choice('урок|урока|уроков', $course->lessons_count) }}
                        @if($course->duration_minutes)
                            · ~{{ round($course->duration_minutes / 60, 1) }} ч
                        @endif
                    </div>

                    @if($course->is_locked)
                        <div class="level-req">🔒 Требуется уровень {{ $course->required_level }}</div>
                    @else
                        <div class="progress-row">
                            <div class="progress-bg">
                                <div class="progress-fill {{ $isDone ? 'pf-green' : $pfClass }}"
                                     style="width:{{ $progress }}%"></div>
                            </div>
                            <span class="progress-label">
                                @if($isDone) ✓ Пройден
                                @elseif($progress > 0) {{ $course->completed_count }}/{{ $course->lessons_count }}
                                @else Начать
                                @endif
                            </span>
                        </div>
                    @endif
                </a>
            @endforeach
        </div>
        @endif
    @endforeach
</div>
@endsection
