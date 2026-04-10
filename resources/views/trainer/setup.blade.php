@extends('layouts.app')
@section('title', 'Выбор цвета')

@push('styles')
<style>
.setup-page {
    min-height: 80vh; display: flex; align-items: center; justify-content: center;
}
.setup-card {
    background: var(--color-surface);
    border-radius: 20px;
    border: 0.5px solid var(--color-border);
    padding: 2.5rem 2rem;
    max-width: 480px; width: 100%; text-align: center;
}
.setup-icon { font-size: 2.5rem; margin-bottom: 1rem; }
.setup-card h1 { font-size: 1.4rem; font-weight: 500; margin-bottom: 0.4rem; color: var(--color-text); }
.setup-card p  { color: var(--color-muted); margin-bottom: 2rem; font-size: 0.9rem; }

.color-options {
    display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;
}
.color-option {
    border: 0.5px solid var(--color-border);
    border-radius: 16px;
    padding: 1.75rem 1rem;
    cursor: pointer;
    transition: all 0.18s;
    background: var(--color-bg);
    text-align: center;
    color: var(--color-text);
}
.color-option:hover {
    border-color: var(--color-muted);
    transform: translateY(-2px);
}
.color-option.selected-white {
    border: 1.5px solid #6b63d4;
    background: rgba(79,70,229,0.07);
    box-shadow: 0 0 0 3px rgba(79,70,229,0.08);
}
[data-theme="dark"] .color-option.selected-white {
    background: rgba(107,99,212,0.15);
}
.color-option.selected-black {
    border: 1.5px solid var(--color-muted);
    background: #1a1a2e;
    color: #f0efe8;
}
[data-theme="light"] .color-option.selected-black {
    box-shadow: 0 0 0 3px rgba(26,26,46,0.1);
}

.color-piece { font-size: 3rem; line-height: 1; display: block; margin-bottom: 0.6rem; }
.color-name  { font-size: 0.95rem; font-weight: 500; display: block; margin-bottom: 0.2rem; }
.color-desc  { font-size: 0.75rem; opacity: 0.55; display: block; }

.random-btn {
    width: 100%; padding: 0.7rem; border-radius: 10px; margin-bottom: 0.75rem;
    border: 0.5px dashed var(--color-border);
    background: transparent; cursor: pointer;
    font-size: 0.88rem; color: var(--color-muted);
    transition: all 0.15s; font-family: inherit;
}
.random-btn:hover {
    border-color: var(--color-muted);
    color: var(--color-text);
    background: var(--color-bg);
}

.start-btn {
    width: 100%; padding: 0.9rem; border-radius: 12px; border: none;
    background: #4f46e5; color: white;
    font-size: 0.95rem; font-weight: 500;
    cursor: pointer; transition: background 0.18s, opacity 0.18s;
    opacity: 0.35; pointer-events: none; font-family: inherit;
}
.start-btn.ready { opacity: 1; pointer-events: all; }
.start-btn.ready:hover { background: #4338ca; }
.start-btn.black-ready { background: #1a1a2e; }
.start-btn.black-ready:hover { background: #0f0f1e; }
</style>
@endpush

@section('content')
<div class="setup-page">
    <div class="setup-card">
        <div class="setup-icon">♟</div>
        <h1>Новая партия с тренером</h1>
        <p>Выбери за какие фигуры будешь играть</p>

        <div class="color-options">
            <div class="color-option" id="opt-white" onclick="selectColor('white')">
                <span class="color-piece">♔</span>
                <span class="color-name">Белые</span>
                <span class="color-desc">Ход первым</span>
            </div>
            <div class="color-option" id="opt-black" onclick="selectColor('black')">
                <span class="color-piece">♚</span>
                <span class="color-name">Чёрные</span>
                <span class="color-desc">Гарри ходит первым</span>
            </div>
        </div>

        <button class="random-btn" onclick="selectColor(Math.random() > 0.5 ? 'white' : 'black')">
            🎲 Случайный цвет
        </button>

        <button class="start-btn" id="startBtn" onclick="startGame()">
            Начать партию →
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedColor = null;

function selectColor(color) {
    selectedColor = color;
    document.getElementById('opt-white').className = 'color-option' + (color === 'white' ? ' selected-white' : '');
    document.getElementById('opt-black').className = 'color-option' + (color === 'black' ? ' selected-black' : '');
    const btn = document.getElementById('startBtn');
    btn.className = 'start-btn ready' + (color === 'black' ? ' black-ready' : '');
    btn.textContent = color === 'white' ? '♔ Играть белыми →' : '♚ Играть чёрными →';
}

function startGame() {
    if (!selectedColor) return;
    window.location.href = '/trainer?color=' + selectedColor;
}
</script>
@endpush