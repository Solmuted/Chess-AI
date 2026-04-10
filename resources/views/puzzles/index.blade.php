@extends('layouts.app')
@section('title', 'Тактические задачи')

@push('styles')
<style>
.pz-wrap { max-width: 960px; margin: 0 auto; padding: 1.5rem 1rem; }
.pz-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.2rem; }
.pz-title { font-size: 1.2rem; font-weight: 600; color: var(--color-text); }
.pz-stats { font-size: 0.82rem; color: var(--color-muted); }
.filter-row { display: flex; gap: 8px; margin-bottom: 1.2rem; flex-wrap: wrap; }
.filter-btn {
    padding: 5px 14px; border-radius: 20px; border: 0.5px solid var(--color-border);
    background: none; color: var(--color-muted); font-size: 0.82rem; cursor: pointer;
    transition: all .15s; font-family: inherit;
}
.filter-btn.active, .filter-btn:hover {
    background: var(--color-surface); color: var(--color-text); border-color: var(--color-muted);
}
.puzzles-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px; }
.puzzle-card {
    background: var(--color-surface); border: 0.5px solid var(--color-border);
    border-radius: 14px; padding: 14px; text-decoration: none; display: block;
    transition: border-color .15s, transform .15s; position: relative;
}
.puzzle-card:hover { border-color: var(--color-muted); transform: translateY(-2px); }
.puzzle-card.solved { border-color: rgba(29,158,117,0.4); }
.solved-badge {
    position: absolute; top: 10px; right: 10px;
    font-size: 0.7rem; background: rgba(29,158,117,0.15); color: #1D9E75;
    padding: 2px 8px; border-radius: 10px;
}
.puzzle-board {
    width: 100%; aspect-ratio: 1; background: #b58863;
    border-radius: 6px; margin-bottom: 10px;
    display: grid; grid-template-columns: repeat(8, 1fr); overflow: hidden;
}
.pb-cell { aspect-ratio: 1; }
.pb-cell.light { background: #f0d9b5; }
.pb-cell.dark  { background: #b58863; }
.pb-piece { display: flex; align-items: center; justify-content: center; font-size: 11px; }
.puzzle-name { font-size: 0.85rem; font-weight: 500; color: var(--color-text); margin-bottom: 4px; }
.puzzle-meta { font-size: 0.75rem; color: var(--color-muted); display: flex; align-items: center; gap: 5px; }
.diff-dot { width: 6px; height: 6px; border-radius: 50%; display: inline-block; flex-shrink: 0; }
.diff-easy { background: #1D9E75; }
.diff-medium { background: #BA7517; }
.diff-hard { background: #993C1D; }
@media (max-width: 600px) { .puzzles-grid { grid-template-columns: repeat(2, 1fr); } }
</style>
@endpush

@section('content')
<div class="pz-wrap">
    <div class="pz-header">
        <div class="pz-title">⚔ Тактические задачи</div>
        <div class="pz-stats">Решено: {{ $solvedCount }} / {{ $totalCount }}</div>
    </div>

    <div class="filter-row">
        <button class="filter-btn active" onclick="filterPuzzles('all', this)">Все</button>
        <button class="filter-btn" onclick="filterPuzzles('easy', this)">Лёгкие</button>
        <button class="filter-btn" onclick="filterPuzzles('medium', this)">Средние</button>
        <button class="filter-btn" onclick="filterPuzzles('hard', this)">Сложные</button>
        <button class="filter-btn" onclick="filterPuzzles('unsolved', this)">Не решённые</button>
    </div>

    <div class="puzzles-grid" id="puzzles-grid">
        @forelse($puzzles as $puzzle)
            @php
                $dotClass   = match($puzzle->difficulty) { 'easy'=>'diff-easy','medium'=>'diff-medium','hard'=>'diff-hard',default=>'diff-easy' };
                $diffLabel  = match($puzzle->difficulty) { 'easy'=>'Лёгкая','medium'=>'Средняя','hard'=>'Сложная',default=>'' };
            @endphp
            <a href="/puzzle/{{ $puzzle->id }}"
               class="puzzle-card {{ $puzzle->is_solved ? 'solved' : '' }}"
               data-diff="{{ $puzzle->difficulty }}"
               data-solved="{{ $puzzle->is_solved ? '1' : '0' }}">
                @if($puzzle->is_solved)
                    <span class="solved-badge">✓ Решена</span>
                @endif
                <div class="puzzle-board" id="pb-{{ $puzzle->id }}"></div>
                <div class="puzzle-name">{{ $puzzle->title }}</div>
                <div class="puzzle-meta">
                    <span class="diff-dot {{ $dotClass }}"></span>
                    {{ $diffLabel }}
                    @if($puzzle->rating_range) · {{ $puzzle->rating_range }} @endif
                </div>
            </a>
        @empty
            <p style="color:var(--color-muted);font-size:0.88rem;grid-column:1/-1;">
                Задачи пока не добавлены. Добавьте их через сидер или вручную в БД.
            </p>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
// Рендер мини-досок
document.querySelectorAll('.puzzle-board').forEach(el => {
    for (let r = 0; r < 8; r++) {
        for (let c = 0; c < 8; c++) {
            const cell = document.createElement('div');
            cell.className = 'pb-cell ' + ((r + c) % 2 === 0 ? 'light' : 'dark');
            el.appendChild(cell);
        }
    }
});

// Фильтрация
function filterPuzzles(type, btn) {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.puzzle-card').forEach(card => {
        const show =
            type === 'all' ||
            (type === 'unsolved' && card.dataset.solved === '0') ||
            card.dataset.diff === type;
        card.style.display = show ? 'block' : 'none';
    });
}
</script>
@endpush