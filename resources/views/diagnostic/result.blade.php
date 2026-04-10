@extends('layouts.app')
@section('title', 'Результат диагностики')

@push('styles')
<style>
.result-wrap { max-width: 760px; margin: 0 auto; }

.result-hero {
    background: var(--color-surface);
    border: 0.5px solid var(--color-border);
    border-radius: 20px;
    padding: 36px 40px;
    text-align: center;
    margin-bottom: 28px;
}
.result-hero h1 { font-size: 1.5rem; font-weight: 500; color: var(--color-text); margin-bottom: 16px; }

.level-badge {
    display: inline-block;
    background: rgba(240,192,64,0.15);
    color: #b8922e;
    font-size: 1.2rem;
    font-weight: 500;
    padding: 8px 28px;
    border-radius: 50px;
    margin-bottom: 24px;
}
[data-theme="dark"] .level-badge { color: #f0c040; }

.stats-row {
    display: flex; justify-content: center; gap: 48px;
    flex-wrap: wrap;
}
.stat-box { text-align: center; }
.stat-box .value { font-size: 1.8rem; font-weight: 500; color: #f0c040; }
.stat-box .label { font-size: 0.82rem; color: var(--color-muted); margin-top: 4px; }

.section-title {
    font-size: 1.1rem; font-weight: 500;
    color: var(--color-text);
    margin-bottom: 14px;
}

.questions-list { display: flex; flex-direction: column; gap: 12px; }

.q-card {
    background: var(--color-surface);
    border: 0.5px solid var(--color-border);
    border-radius: 14px;
    padding: 18px 22px;
    border-left: 3px solid;
}
.q-card.correct { border-left-color: #38a169; }
.q-card.wrong   { border-left-color: #e53e3e; }

.q-title {
    font-size: 0.9rem;
    font-weight: 500;
    color: var(--color-text);
    margin-bottom: 10px;
    line-height: 1.5;
}
.answer-row {
    display: flex; align-items: center; gap: 8px;
    font-size: 0.88rem; color: var(--color-text);
    margin: 4px 0;
}

.badge {
    font-size: 0.72rem; font-weight: 500;
    padding: 2px 10px; border-radius: 20px;
    white-space: nowrap; flex-shrink: 0;
}
.badge.your  { background: rgba(229,62,62,0.12);  color: #e53e3e; }
.badge.right { background: rgba(56,161,105,0.12); color: #38a169; }
.badge.ok    { background: rgba(56,161,105,0.12); color: #38a169; }

.result-footer { text-align: center; margin-top: 28px; }
.btn-primary {
    display: inline-block;
    background: #4f46e5; color: white;
    border: none; padding: 12px 32px;
    border-radius: 12px; font-size: 0.95rem; font-weight: 500;
    cursor: pointer; text-decoration: none;
    transition: background 0.15s;
}
.btn-primary:hover { background: #4338ca; }
</style>
@endpush

@section('content')
<div class="result-wrap">

    <div class="result-hero">
        <h1>Диагностика завершена!</h1>
        @php
            $names = [1=>'Новичок',2=>'Начинающий',3=>'Средний',4=>'Продвинутый',5=>'Эксперт'];
        @endphp
        <div class="level-badge">♟ Уровень {{ $level }} — {{ $names[$level] }}</div>
        <div class="stats-row">
            <div class="stat-box">
                <div class="value">{{ $score }}</div>
                <div class="label">Баллов набрано</div>
            </div>
            <div class="stat-box">
                <div class="value">{{ $rating }}</div>
                <div class="label">Рейтинг</div>
            </div>
            <div class="stat-box">
                @php
                    $correct = collect($questions)->filter(fn($q,$id) => ($answers[$id] ?? '') === $q['correct'])->count();
                @endphp
                <div class="value">{{ $correct }} / {{ count($questions) }}</div>
                <div class="label">Правильных ответов</div>
            </div>
        </div>
    </div>

    <div class="section-title">Разбор ответов</div>
    <div class="questions-list">
        @foreach($questions as $id => $q)
            @php
                $userAnswer = $answers[$id] ?? '—';
                $isCorrect  = $userAnswer === $q['correct'];
            @endphp
            <div class="q-card {{ $isCorrect ? 'correct' : 'wrong' }}">
                <div class="q-title">{{ $loop->iteration }}. {{ $q['text'] }}</div>
                @if($isCorrect)
                    <div class="answer-row">
                        <span class="badge ok">✓ Верно</span>
                        <span>{{ $userAnswer }}</span>
                    </div>
                @else
                    <div class="answer-row">
                        <span class="badge your">✗ Ваш ответ</span>
                        <span>{{ $userAnswer }}</span>
                    </div>
                    <div class="answer-row">
                        <span class="badge right">✓ Правильно</span>
                        <span>{{ $q['correct'] }}</span>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="result-footer">
        <a href="/dashboard" class="btn-primary">Перейти к обучению →</a>
    </div>

</div>
@endsection

