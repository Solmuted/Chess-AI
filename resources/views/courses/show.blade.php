@extends('layouts.app')
@section('title', $course->title)

@push('styles')
<style>
.cs-wrap { max-width: 760px; margin: 0 auto; padding: 1.5rem 1rem 3rem; }
.cs-back { font-size: 0.8rem; color: var(--color-muted); text-decoration: none; display:inline-flex; align-items:center; gap:5px; margin-bottom:1.2rem; transition:color .15s; }
.cs-back:hover { color: var(--color-text); }

.cs-hero {
    background: var(--color-surface);
    border: 0.5px solid var(--color-border);
    border-radius: 16px; padding: 24px; margin-bottom: 20px;
    position: relative; overflow: hidden;
}
.cs-hero::before {
    content: ''; position: absolute; top:0; left:0; right:0; height:3px; border-radius:16px 16px 0 0;
}
.beginner   .cs-hero::before { background: linear-gradient(90deg,#3dba82,#5dcaa5); }
.intermediate .cs-hero::before { background: linear-gradient(90deg,#e8b84b,#f0c040); }
.advanced   .cs-hero::before { background: linear-gradient(90deg,#e05a5a,#e87a5a); }

.cs-title { font-size: 1.3rem; font-weight: 600; color: var(--color-text); margin-bottom: 6px; }
.cs-meta  { font-size: 0.8rem; color: var(--color-muted); margin-bottom: 16px; }
.cs-progress-row { display:flex; align-items:center; gap:12px; }
.cs-progress-bg { flex:1; height:6px; background:var(--color-border); border-radius:3px; overflow:hidden; }
.cs-progress-fill { height:100%; border-radius:3px; background:#5a7fe8; transition:width .5s ease; }
.cs-progress-label { font-size:0.78rem; color:var(--color-muted); white-space:nowrap; }

.lessons-label { font-size:0.72rem; text-transform:uppercase; letter-spacing:.07em; color:var(--color-muted); font-weight:500; margin-bottom:12px; }

.lesson-list { display:flex; flex-direction:column; gap:8px; }
.lesson-row {
    background: var(--color-surface);
    border: 0.5px solid var(--color-border);
    border-radius: 12px; padding: 14px 16px;
    text-decoration: none; display:flex; align-items:center; gap:14px;
    transition: border-color .15s, transform .15s;
}
.lesson-row:hover:not(.locked) { border-color:var(--color-muted); transform:translateY(-1px); }
.lesson-row.locked { opacity:0.55; pointer-events:none; }
.lesson-row.completed { border-color:rgba(61,186,130,0.3); }

.ln-num {
    width:34px; height:34px; border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    font-size:0.8rem; font-weight:500; flex-shrink:0;
}
.ln-done    { background:rgba(61,186,130,0.12); color:#3dba82; }
.ln-active  { background:rgba(90,127,232,0.12); color:#5a7fe8; }
.ln-locked  { background:var(--color-border); color:var(--color-muted); }

.ln-title  { font-size:0.88rem; font-weight:500; color:var(--color-text); margin-bottom:2px; }
.ln-desc   { font-size:0.76rem; color:var(--color-muted); }
.ln-badge  {
    margin-left:auto; flex-shrink:0;
    font-size:0.7rem; padding:3px 10px; border-radius:20px;
}
.lb-done  { background:rgba(61,186,130,0.12); color:#3dba82; }
.lb-prog  { background:rgba(90,127,232,0.12); color:#5a7fe8; }
.lb-new   { background:var(--color-border); color:var(--color-muted); }

.topic-icons { 'basics':'♟','opening':'🏰','tactics':'⚔','endgame':'👑','strategy':'🧠' }
</style>
@endpush

@section('content')
<div class="cs-wrap {{ $course->difficulty }}">
    <a href="/courses" class="cs-back">← Все курсы</a>

    <div class="cs-hero">
        <div class="cs-title">{{ $course->title }}</div>
        <div class="cs-meta">
            {{ $lessons->count() }} {{ trans_choice('урок|урока|уроков', $lessons->count()) }}
            @if($course->duration_minutes) · ~{{ round($course->duration_minutes/60,1) }} ч @endif
            · {{ ['beginner'=>'Новичок','intermediate'=>'Средний','advanced'=>'Продвинутый'][$course->difficulty] ?? '' }}
        </div>
        <div class="cs-progress-row">
            <div class="cs-progress-bg">
                <div class="cs-progress-fill" style="width:{{ $progress }}%"></div>
            </div>
            <span class="cs-progress-label">{{ $completed }}/{{ $lessons->count() }} пройдено</span>
        </div>
    </div>

    <div class="lessons-label">Уроки курса</div>
    <div class="lesson-list">
        @foreach($lessons as $lesson)
            @php
                $status = $lesson->status ?? 'not_started';
                $numClass = match($status) { 'completed'=>'ln-done','in_progress'=>'ln-active',default=>'ln-locked' };
                $numLabel = match($status) { 'completed'=>'✓','in_progress'=>'→',default=>$lesson->order_in_course };
                $topicIcon = ['basics'=>'♟','opening'=>'🏰','tactics'=>'⚔','endgame'=>'👑','strategy'=>'🧠'][$lesson->topic] ?? '♟';
            @endphp
            <a href="/lesson/{{ $lesson->id }}"
               class="lesson-row {{ $status === 'completed' ? 'completed' : '' }}">
                <div class="ln-num {{ $numClass }}">{{ $numLabel }}</div>
                <div style="flex:1;min-width:0">
                    <div class="ln-title">{{ $topicIcon }} {{ $lesson->title }}</div>
                    @if($lesson->description)
                        <div class="ln-desc">{{ Str::limit($lesson->description, 80) }}</div>
                    @endif
                </div>
                <span class="ln-badge {{ match($status) { 'completed'=>'lb-done','in_progress'=>'lb-prog',default=>'lb-new' } }}">
                    {{ match($status) { 'completed'=>'✓ Пройден','in_progress'=>'В процессе',default=>'Начать' } }}
                </span>
            </a>
        @endforeach
    </div>
</div>
@endsection
