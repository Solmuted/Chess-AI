@extends('layouts.app')
@section('title', 'Диагностика уровня')

@push('styles')
<style>
.diag-wrap { max-width: 700px; margin: 0 auto; }

.diag-header { margin-bottom: 28px; }
.diag-header h1 { font-size: 1.5rem; font-weight: 500; color: var(--color-text); margin-bottom: 6px; }
.diag-header p  { color: var(--color-muted); font-size: 0.9rem; }

.question-card {
    background: var(--color-surface);
    border: 0.5px solid var(--color-border);
    border-radius: 16px;
    padding: 20px 24px;
    margin-bottom: 16px;
}
.question-text {
    font-size: 0.95rem;
    font-weight: 500;
    color: var(--color-text);
    margin-bottom: 14px;
    line-height: 1.5;
}
.options { display: flex; flex-direction: column; gap: 8px; }

.option-label {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 14px;
    border-radius: 10px;
    border: 0.5px solid var(--color-border);
    cursor: pointer;
    font-size: 0.88rem;
    color: var(--color-text);
    transition: background 0.15s, border-color 0.15s;
}
.option-label:hover {
    background: var(--color-bg);
    border-color: var(--color-muted);
}
.option-label input[type="radio"] { accent-color: #4f46e5; flex-shrink: 0; }

.btn-submit {
    display: inline-block;
    margin-top: 8px;
    padding: 0.85rem 2rem;
    border-radius: 12px;
    border: none;
    background: #4f46e5;
    color: white;
    font-size: 0.95rem;
    font-weight: 500;
    cursor: pointer;
    font-family: inherit;
    transition: background 0.15s;
}
.btn-submit:hover { background: #4338ca; }
</style>
@endpush

@section('content')
<div class="diag-wrap">
    <div class="diag-header">
        <h1>Диагностика уровня</h1>
        <p>Ответь на вопросы — тренер подберёт программу обучения</p>
    </div>

    <form method="POST" action="/diagnostic">
        @csrf
        @foreach($questions as $id => $q)
            <div class="question-card">
                <p class="question-text">{{ $loop->iteration }}. {{ $q['text'] }}</p>
                <div class="options">
                    @foreach($q['options'] as $option)
                        <label class="option-label">
                            <input type="radio" name="answers[{{ $id }}]" value="{{ $option }}" required>
                            <span>{{ $option }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach
        <button type="submit" class="btn-submit">Узнать мой уровень →</button>
    </form>
</div>
@endsection

