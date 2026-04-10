@extends('layouts.app')
@section('title', $puzzle->title . ' — Тактика')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=DM+Mono:ital,wght@0,400;0,500;1,400&family=Fraunces:opsz,wght@9..144,300;9..144,500;9..144,600&display=swap" rel="stylesheet">
<style>
:root {
    --sq-light: #f0d9b5;
    --sq-dark:  #b58863;
    --sq-sel:   rgba(106,153,255,0.55);
    --sq-move:  rgba(106,153,255,0.32);
    --sq-last:  rgba(255,215,0,0.38);
    --sq-hint:  rgba(80,200,130,0.42);
    --sq-check: rgba(220,60,50,0.45);
    --accent:   #5a7fe8;
    --gold:     #e8b84b;
    --green:    #3dba82;
    --red:      #e05a5a;
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.pz-shell {
    max-width: 1100px;
    margin: 0 auto;
    padding: 1.4rem 1rem 3rem;
    font-family: system-ui, sans-serif;
}

/* ── Back link ── */
.pz-back {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 0.8rem; color: var(--color-muted);
    text-decoration: none; margin-bottom: 1.4rem;
    transition: color .15s;
}
.pz-back:hover { color: var(--color-text); }

/* ── Layout ── */
.pz-layout {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 28px;
    align-items: start;
}
@media (max-width: 820px) {
    .pz-layout { grid-template-columns: 1fr; }
}

/* ── Board wrapper ── */
.board-col { display: flex; flex-direction: column; align-items: center; gap: 12px; }

.board-meta {
    width: 100%;
    display: flex; justify-content: space-between; align-items: center;
    font-size: 0.78rem; color: var(--color-muted);
}
.diff-pill {
    padding: 3px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 500;
}
.diff-easy   { background: rgba(61,186,130,0.15); color: #3dba82; }
.diff-medium { background: rgba(232,184,75,0.15);  color: #c9951c; }
.diff-hard   { background: rgba(220,90,90,0.15);   color: #e05a5a; }

/* ── Chess board ── */
.board-wrap {
    position: relative;
    width: min(480px, 92vw);
    aspect-ratio: 1;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 12px 40px rgba(0,0,0,0.25), 0 2px 8px rgba(0,0,0,0.15);
    user-select: none;
}
.chess-board {
    width: 100%; height: 100%;
    display: grid;
    grid-template-columns: repeat(8, 1fr);
}
.sq {
    aspect-ratio: 1;
    display: flex; align-items: center; justify-content: center;
    position: relative;
    cursor: pointer;
    transition: background .08s;
}
.sq.light { background: var(--sq-light); }
.sq.dark  { background: var(--sq-dark); }
.sq.selected::after    { content:''; position:absolute; inset:0; background: var(--sq-sel); pointer-events:none; }
.sq.movable::after     { content:''; position:absolute; inset:0; background: var(--sq-move); pointer-events:none; }
.sq.last-from::after,
.sq.last-to::after     { content:''; position:absolute; inset:0; background: var(--sq-last); pointer-events:none; }
.sq.hint-sq::after     { content:''; position:absolute; inset:0; background: var(--sq-hint); pointer-events:none; }
.sq.in-check::after    { content:''; position:absolute; inset:0; background: var(--sq-check); pointer-events:none; }

/* Coordinate labels */
.sq .coord-file {
    position: absolute; bottom: 2px; right: 3px;
    font-size: clamp(7px,1.4vw,10px); font-weight: 500; opacity: 0.55;
    pointer-events: none; line-height: 1;
    color: inherit;
}
.sq.light .coord-file { color: var(--sq-dark); }
.sq.dark  .coord-file { color: var(--sq-light); }
.sq .coord-rank {
    position: absolute; top: 2px; left: 3px;
    font-size: clamp(7px,1.4vw,10px); font-weight: 500; opacity: 0.55;
    pointer-events: none; line-height: 1;
}
.sq.light .coord-rank { color: var(--sq-dark); }
.sq.dark  .coord-rank { color: var(--sq-light); }

/* Move dot / capture ring for legal moves */
.sq.can-move .move-dot {
    width: 28%; height: 28%; border-radius: 50%;
    background: rgba(0,0,0,0.18);
    pointer-events: none;
    animation: dotPop .12s ease;
}
.sq.can-capture .move-dot {
    width: 88%; height: 88%; border-radius: 50%;
    border: 5px solid rgba(0,0,0,0.2);
    background: transparent;
    pointer-events: none;
    animation: dotPop .12s ease;
}
@keyframes dotPop { from { transform: scale(0); } to { transform: scale(1); } }

/* Piece */
.piece {
    font-size: clamp(22px, 5.5vw, 44px);
    line-height: 1;
    cursor: grab;
    position: relative; z-index: 2;
    transition: transform .1s;
    filter: drop-shadow(0 1px 3px rgba(0,0,0,0.3));
}
.piece:active { cursor: grabbing; }
.piece.dragging { opacity: 0; }

/* Drag ghost */
#drag-ghost {
    position: fixed; pointer-events: none; z-index: 9999;
    font-size: clamp(30px,7vw,54px);
    transform: translate(-50%, -50%);
    filter: drop-shadow(0 4px 12px rgba(0,0,0,0.4));
    transition: none;
    display: none;
}

/* Board overlay for solved/wrong flash */
.board-flash {
    position: absolute; inset: 0;
    pointer-events: none; z-index: 10;
    opacity: 0; border-radius: 8px;
    transition: opacity .15s;
}
.board-flash.flash-correct { background: rgba(61,186,130,0.3); }
.board-flash.flash-wrong   { background: rgba(220,60,50,0.3); }
.board-flash.show { opacity: 1; }

/* ── Side panel ── */
.side-col { display: flex; flex-direction: column; gap: 14px; min-width: 0; }

.panel {
    background: var(--color-surface);
    border: 0.5px solid var(--color-border);
    border-radius: 14px;
    padding: 16px 18px;
}

/* Puzzle header */
.pz-title {
    font-family: 'Fraunces', Georgia, serif;
    font-size: 1.4rem; font-weight: 600;
    color: var(--color-text); margin-bottom: 4px;
}
.pz-subtitle { font-size: 0.8rem; color: var(--color-muted); }

/* Turn indicator */
.turn-row {
    display: flex; align-items: center; gap: 8px;
    font-size: 0.82rem; color: var(--color-muted);
    padding: 10px 14px;
    background: var(--color-bg);
    border-radius: 10px; margin-top: 10px;
}
.turn-dot {
    width: 12px; height: 12px; border-radius: 50%;
    border: 1.5px solid rgba(0,0,0,0.15);
    flex-shrink: 0;
}
.turn-dot.white { background: #f5f5f0; }
.turn-dot.black { background: #2a2a2a; }

/* Action buttons */
.btn-row { display: flex; gap: 8px; flex-wrap: wrap; }
.btn {
    padding: 8px 14px; border-radius: 10px;
    font-size: 0.82rem; font-weight: 500;
    cursor: pointer; border: none;
    font-family: inherit; transition: all .15s;
    display: inline-flex; align-items: center; gap: 6px;
}
.btn-primary { background: var(--accent); color: #fff; }
.btn-primary:hover { background: #4a6fd4; transform: translateY(-1px); }
.btn-ghost {
    background: var(--color-bg);
    border: 0.5px solid var(--color-border);
    color: var(--color-muted);
}
.btn-ghost:hover { color: var(--color-text); border-color: var(--color-muted); }
.btn-hint { color: var(--green); border-color: rgba(61,186,130,0.3); background: rgba(61,186,130,0.07); }
.btn-hint:hover { background: rgba(61,186,130,0.14); }
.btn-ai { color: var(--accent); border-color: rgba(90,127,232,0.3); background: rgba(90,127,232,0.07); }
.btn-ai:hover { background: rgba(90,127,232,0.14); }
.btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none !important; }

/* Feedback */
.feedback-box {
    border-radius: 10px; padding: 12px 14px;
    font-size: 0.84rem; display: none; animation: fadeIn .2s ease;
}
.feedback-box.correct {
    background: rgba(61,186,130,0.12);
    border: 0.5px solid rgba(61,186,130,0.3);
    color: var(--green);
    display: block;
}
.feedback-box.wrong {
    background: rgba(220,80,80,0.1);
    border: 0.5px solid rgba(220,80,80,0.25);
    color: var(--red);
    display: block;
}
.feedback-box strong { display: block; margin-bottom: 2px; font-size: 0.88rem; }

/* Move history */
.moves-label {
    font-size: 0.72rem; text-transform: uppercase; letter-spacing: .06em;
    color: var(--color-muted); margin-bottom: 8px; font-weight: 500;
}
.moves-list {
    display: flex; flex-wrap: wrap; gap: 4px;
    min-height: 28px;
}
.move-chip {
    background: var(--color-bg);
    border: 0.5px solid var(--color-border);
    border-radius: 6px; padding: 3px 8px;
    font-size: 0.78rem; font-family: 'DM Mono', monospace;
    color: var(--color-text);
}

/* AI chat */
.ai-panel { padding: 0; overflow: hidden; }
.ai-header {
    display: flex; align-items: center; gap: 10px;
    padding: 14px 18px 12px;
    border-bottom: 0.5px solid var(--color-border);
}
.ai-avatar {
    width: 30px; height: 30px; border-radius: 8px;
    background: linear-gradient(135deg, #5a7fe8, #9b59e8);
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; flex-shrink: 0;
}
.ai-name { font-size: 0.85rem; font-weight: 500; color: var(--color-text); }
.ai-status { font-size: 0.72rem; color: var(--green); }

.ai-messages {
    padding: 12px 18px;
    max-height: 260px; overflow-y: auto;
    display: flex; flex-direction: column; gap: 10px;
    scroll-behavior: smooth;
}
.ai-messages::-webkit-scrollbar { width: 4px; }
.ai-messages::-webkit-scrollbar-track { background: transparent; }
.ai-messages::-webkit-scrollbar-thumb { background: var(--color-border); border-radius: 4px; }

.msg {
    font-size: 0.82rem; line-height: 1.55;
    animation: fadeIn .25s ease;
}
.msg.from-ai {
    background: var(--color-bg);
    border: 0.5px solid var(--color-border);
    border-radius: 0 10px 10px 10px;
    padding: 10px 12px;
    color: var(--color-text);
}
.msg.from-user {
    background: rgba(90,127,232,0.12);
    border: 0.5px solid rgba(90,127,232,0.2);
    border-radius: 10px 0 10px 10px;
    padding: 10px 12px;
    color: var(--color-text);
    align-self: flex-end;
    max-width: 85%;
}
.msg-typing { display: flex; align-items: center; gap: 4px; padding: 10px 12px; }
.msg-typing span {
    width: 6px; height: 6px; border-radius: 50%;
    background: var(--color-muted); animation: blink 1.2s infinite;
}
.msg-typing span:nth-child(2) { animation-delay: .2s; }
.msg-typing span:nth-child(3) { animation-delay: .4s; }
@keyframes blink { 0%,80%,100% { opacity:.2; } 40% { opacity:1; } }

.ai-input-row {
    display: flex; gap: 8px;
    padding: 12px 18px 14px;
    border-top: 0.5px solid var(--color-border);
}
.ai-input {
    flex: 1; padding: 8px 12px; border-radius: 8px;
    border: 0.5px solid var(--color-border);
    background: var(--color-bg); color: var(--color-text);
    font-size: 0.82rem; font-family: inherit; outline: none;
    transition: border-color .15s;
}
.ai-input:focus { border-color: var(--accent); }
.ai-send {
    padding: 8px 14px; border-radius: 8px;
    background: var(--accent); color: #fff;
    border: none; cursor: pointer; font-size: 0.82rem;
    font-family: inherit; transition: background .15s;
}
.ai-send:hover { background: #4a6fd4; }

/* Progress bar */
.puzzle-progress {
    height: 3px; background: var(--color-border);
    border-radius: 2px; overflow: hidden; margin-top: 8px;
}
.puzzle-progress-fill {
    height: 100%; background: var(--accent);
    border-radius: 2px; transition: width .4s ease;
}

/* Solved state */
.solved-banner {
    display: none; text-align: center; padding: 20px;
    animation: fadeIn .3s ease;
}
.solved-banner.show { display: block; }
.solved-emoji { font-size: 2.5rem; margin-bottom: 8px; }
.solved-title {
    font-family: 'Fraunces', serif;
    font-size: 1.2rem; font-weight: 600;
    color: var(--green); margin-bottom: 4px;
}
.solved-sub { font-size: 0.8rem; color: var(--color-muted); }
.rating-gain {
    display: inline-block; margin-top: 10px;
    padding: 4px 14px; border-radius: 20px;
    background: rgba(61,186,130,0.15); color: var(--green);
    font-size: 0.82rem; font-weight: 500;
}

@keyframes fadeIn { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: none; } }

/* Next puzzle button */
.btn-next {
    background: var(--green); color: #fff;
    padding: 10px 20px; border-radius: 10px;
    font-size: 0.85rem; font-weight: 500;
    margin-top: 12px; cursor: pointer; border: none;
    font-family: inherit; transition: background .15s;
    display: inline-flex; align-items: center; gap: 6px;
}
.btn-next:hover { background: #32a870; }

/* Already solved */
.already-solved {
    background: rgba(61,186,130,0.1);
    border: 0.5px solid rgba(61,186,130,0.3);
    border-radius: 10px; padding: 10px 14px;
    font-size: 0.82rem; color: var(--green);
}
</style>
@endpush

@section('content')
<div class="pz-shell">
    <a href="/puzzles" class="pz-back">← Все задачи</a>

    <div class="pz-layout">

        {{-- ── Left: Board ── --}}
        <div class="board-col">
            <div class="board-meta">
                <span id="turn-label" style="font-size:0.78rem;color:var(--color-muted)">Загрузка...</span>
                <span class="diff-pill diff-{{ $puzzle->difficulty }}">
                    {{ ['easy'=>'Лёгкая','medium'=>'Средняя','hard'=>'Сложная'][$puzzle->difficulty] ?? '' }}
                    @if($puzzle->rating_range) · {{ $puzzle->rating_range }} @endif
                </span>
            </div>

            <div class="board-wrap" id="board-wrap">
                <div class="chess-board" id="chess-board"></div>
                <div class="board-flash" id="board-flash"></div>
            </div>

            {{-- Board controls --}}
            <div style="display:flex;gap:8px;width:min(480px,92vw)">
                <button class="btn btn-ghost" onclick="flipBoard()" title="Перевернуть доску" style="flex:1">⇅ Повернуть</button>
                <button class="btn btn-ghost" onclick="resetBoard()" title="Сброс позиции" style="flex:1">↺ Сброс</button>
            </div>
        </div>

        {{-- ── Right: Panel ── --}}
        <div class="side-col">

            {{-- Info + actions --}}
            <div class="panel">
                <div class="pz-title">{{ $puzzle->title }}</div>
                <div class="pz-subtitle">Задача #{{ $puzzle->id }}</div>

                @if($isSolved)
                <div class="already-solved" style="margin-top:10px">
                    ✓ Вы уже решили эту задачу! Правильный ход: <strong>{{ $puzzle->solution }}</strong>
                </div>
                @endif

                <div class="turn-row" id="turn-row">
                    <div class="turn-dot" id="turn-dot"></div>
                    <span id="turn-text">Определяем чью очередь...</span>
                </div>

                <div class="puzzle-progress" style="margin-top:10px">
                    <div class="puzzle-progress-fill" id="progress-fill" style="width:0%"></div>
                </div>

                <div style="margin-top:12px">
                    <div class="feedback-box" id="feedback-box"></div>
                </div>

                <div class="solved-banner" id="solved-banner">
                    <div class="solved-emoji">♛</div>
                    <div class="solved-title">Превосходно!</div>
                    <div class="solved-sub">Вы нашли правильный ход</div>
                    <div class="rating-gain">+5 к рейтингу</div><br>
                    <a href="/puzzles" class="btn-next">→ Следующая задача</a>
                </div>

                @if(!$isSolved)
                <div class="btn-row" style="margin-top:14px" id="action-btns">
                    <button class="btn btn-ghost btn-hint" onclick="showHint()" id="btn-hint">💡 Подсказка</button>
                    <button class="btn btn-ghost btn-ai" onclick="askAI('explain')" id="btn-ai">🤖 Объяснить</button>
                    <button class="btn btn-ghost" onclick="giveUp()" id="btn-giveup">🏳 Сдаться</button>
                </div>
                @endif
            </div>

            {{-- Move log --}}
            <div class="panel">
                <div class="moves-label">История ходов</div>
                <div class="moves-list" id="moves-list">
                    <span style="font-size:0.78rem;color:var(--color-muted)">Ходов пока нет</span>
                </div>
            </div>

            {{-- AI Chat --}}
            <div class="panel ai-panel">
                <div class="ai-header">
                    <div class="ai-avatar">♟</div>
                    <div>
                        <div class="ai-name">Гарри — ИИ-тренер</div>
                        <div class="ai-status">● онлайн</div>
                    </div>
                </div>
                <div class="ai-messages" id="ai-messages">
                    <div class="msg from-ai">
                        Привет! Я помогу разобраться с этой задачей. Попробуй сделать ход — перетащи фигуру или нажми на неё. Если застрянешь, нажми <strong>«Объяснить»</strong> или задай вопрос ниже.
                    </div>
                </div>
                <div class="ai-input-row">
                    <input class="ai-input" id="ai-input" placeholder="Задай вопрос тренеру..." maxlength="200"
                           onkeydown="if(event.key==='Enter')sendAIMessage()">
                    <button class="ai-send" onclick="sendAIMessage()">→</button>
                </div>
            </div>

        </div>
    </div>
</div>

<div id="drag-ghost"></div>
@endsection

@push('scripts')
<script>
// ═══════════════════════════════════════════════════════════
// Data
// ═══════════════════════════════════════════════════════════
const PUZZLE_ID  = {{ $puzzle->id }};
const FEN_STR    = @json($puzzle->fen ?? 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1');
const SOLUTION   = @json(strtolower($puzzle->solution ?? ''));
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
const IS_SOLVED  = @json($isSolved);

// Unicode pieces
const PIECE_UNICODE = {
    'K':'♔','Q':'♕','R':'♖','B':'♗','N':'♘','P':'♙',
    'k':'♚','q':'♛','r':'♜','b':'♝','n':'♞','p':'♟',
};
const FILES = ['a','b','c','d','e','f','g','h'];

// ═══════════════════════════════════════════════════════════
// Board state
// ═══════════════════════════════════════════════════════════
let board       = [];   // 8×8 array of piece chars or null
let activeTurn  = 'w';  // whose turn from FEN
let flipped     = false;
let selected    = null; // {row, col}
let legalSquares = [];  // [{row,col}] for selected piece
let moveHistory  = [];
let hintUsed     = false;
let solved       = IS_SOLVED;
let lastMove     = null; // {from:{r,c}, to:{r,c}}

// ── Parse FEN ──────────────────────────────────────────────
function parseFEN(fen) {
    const parts = fen.trim().split(' ');
    const rows  = parts[0].split('/');
    activeTurn  = (parts[1] || 'w');
    const b = [];
    for (const row of rows) {
        const line = [];
        for (const ch of row) {
            if (ch >= '1' && ch <= '8') {
                for (let i = 0; i < parseInt(ch); i++) line.push(null);
            } else {
                line.push(ch);
            }
        }
        // Убедиться что ровно 8 клеток
        while (line.length < 8) line.push(null);
        b.push(line);
    }
    return b;
}

function deepCopy(b) { return b.map(r => [...r]); }

// ── Very simple pseudo-legal move generator ────────────────
// (just checks the piece can reach the square in principle —
//  good enough for puzzle UX; server validates the solution)
function pseudoLegal(b, r, c, turn) {
    const piece = b[r][c];
    if (!piece) return [];
    const isWhite = piece === piece.toUpperCase();
    if ((turn === 'w' && !isWhite) || (turn === 'b' && isWhite)) return [];

    const moves = [];
    const enemy = sq => {
        const p = b[sq.r]?.[sq.c];
        return p && (isWhite ? p === p.toLowerCase() : p === p.toUpperCase());
    };
    const empty = (r2, c2) => r2 >= 0 && r2 < 8 && c2 >= 0 && c2 < 8 && !b[r2][c2];
    const ally  = (r2, c2) => {
        const p = b[r2]?.[c2];
        if (!p) return false;
        return isWhite ? p === p.toUpperCase() : p === p.toLowerCase();
    };
    const add   = (r2, c2) => {
        if (r2 < 0 || r2 > 7 || c2 < 0 || c2 > 7) return false;
        if (ally(r2, c2)) return false;
        moves.push({row: r2, col: c2});
        return !b[r2][c2]; // returns true if square is empty (to continue sliding)
    };

    const type = piece.toUpperCase();

    if (type === 'P') {
        const dir = isWhite ? -1 : 1;
        const start = isWhite ? 6 : 1;
        if (empty(r + dir, c)) {
            moves.push({row: r+dir, col: c});
            if (r === start && empty(r + 2*dir, c))
                moves.push({row: r+2*dir, col: c});
        }
        [-1,1].forEach(dc => {
            if (enemy({r: r+dir, c: c+dc})) moves.push({row: r+dir, col: c+dc});
        });
    } else if (type === 'N') {
        [[-2,-1],[-2,1],[-1,-2],[-1,2],[1,-2],[1,2],[2,-1],[2,1]]
            .forEach(([dr,dc]) => add(r+dr, c+dc));
    } else if (type === 'K') {
        [[-1,-1],[-1,0],[-1,1],[0,-1],[0,1],[1,-1],[1,0],[1,1]]
            .forEach(([dr,dc]) => add(r+dr, c+dc));
    } else if (type === 'R' || type === 'Q') {
        [[0,1],[0,-1],[1,0],[-1,0]].forEach(([dr,dc]) => {
            let nr=r+dr,nc=c+dc;
            while (add(nr,nc)) { nr+=dr; nc+=dc; }
        });
    }
    if (type === 'B' || type === 'Q') {
        [[1,1],[1,-1],[-1,1],[-1,-1]].forEach(([dr,dc]) => {
            let nr=r+dr,nc=c+dc;
            while (add(nr,nc)) { nr+=dr; nc+=dc; }
        });
    }
    return moves;
}

// ── Move notation ──────────────────────────────────────────
function toAlg(r, c) { return FILES[c] + (8 - r); }
function moveToUCI(r1,c1,r2,c2) { return toAlg(r1,c1)+toAlg(r2,c2); }
function moveToSAN(piece, r1,c1,r2,c2, capture) {
    const type = piece.toUpperCase();
    const pfx  = type === 'P' ? '' : type;
    const cap  = capture ? 'x' : '';
    const orig = (type === 'P' && capture) ? FILES[c1] : '';
    return pfx + orig + cap + toAlg(r2,c2);
}

// ═══════════════════════════════════════════════════════════
// Render
// ═══════════════════════════════════════════════════════════
function renderBoard() {
    const el = document.getElementById('chess-board');
    el.innerHTML = '';

    for (let ri = 0; ri < 8; ri++) {
        for (let ci = 0; ci < 8; ci++) {
            const r = flipped ? 7 - ri : ri;
            const c = flipped ? 7 - ci : ci;

            const sq = document.createElement('div');
            const isLight = (ri + ci) % 2 === 0;
            sq.className = 'sq ' + (isLight ? 'light' : 'dark');
            sq.dataset.r = r;
            sq.dataset.c = c;

            // Coordinate labels
            if (ci === 7) {
                const rank = document.createElement('span');
                rank.className = 'coord-rank';
                rank.textContent = flipped ? ri+1 : 8-ri;
                sq.appendChild(rank);
            }
            if (ri === 7) {
                const file = document.createElement('span');
                file.className = 'coord-file';
                file.textContent = FILES[flipped ? 7-ci : ci];
                sq.appendChild(file);
            }

            // Highlight last move
            if (lastMove) {
                if (r === lastMove.from.r && c === lastMove.from.c) sq.classList.add('last-from');
                if (r === lastMove.to.r   && c === lastMove.to.c)   sq.classList.add('last-to');
            }

            // Selected highlight
            if (selected && selected.row === r && selected.col === c)
                sq.classList.add('selected');

            // Legal move indicators
            const isLegal = legalSquares.some(s => s.row === r && s.col === c);
            if (isLegal) {
                sq.classList.add(board[r][c] ? 'can-capture' : 'can-move');
                const dot = document.createElement('div');
                dot.className = 'move-dot';
                sq.appendChild(dot);
            }

            // Piece
            const piece = board[r][c];
            if (piece) {
                const p = document.createElement('span');
                p.className = 'piece';
                p.textContent = PIECE_UNICODE[piece] || piece;
                p.draggable = true;
                p.dataset.r = r;
                p.dataset.c = c;
                setupDrag(p, r, c);
                sq.appendChild(p);
            }

            // Click
            sq.addEventListener('click', () => handleSquareClick(r, c));
            el.appendChild(sq);
        }
    }

    updateTurnUI();
}

function updateTurnUI() {
    const dot  = document.getElementById('turn-dot');
    const text = document.getElementById('turn-text');
    const label = document.getElementById('turn-label');
    if (!dot) return;
    if (activeTurn === 'w') {
        dot.className = 'turn-dot white';
        text.textContent = 'Ход белых — найдите лучший ход';
        label.textContent = 'Ход белых';
    } else {
        dot.className = 'turn-dot black';
        text.textContent = 'Ход чёрных — найдите лучший ход';
        label.textContent = 'Ход чёрных';
    }
}

// ═══════════════════════════════════════════════════════════
// Click interaction
// ═══════════════════════════════════════════════════════════
function handleSquareClick(r, c) {
    if (solved) return;
    const piece = board[r][c];

    // If something selected → try to move
    if (selected) {
        if (selected.row === r && selected.col === c) {
            // Deselect
            selected = null; legalSquares = [];
            renderBoard(); return;
        }
        // Move to legal square
        if (legalSquares.some(s => s.row === r && s.col === c)) {
            doMove(selected.row, selected.col, r, c);
            return;
        }
    }

    // Select own piece
    if (piece) {
        const isWhite = piece === piece.toUpperCase();
        if ((activeTurn === 'w' && isWhite) || (activeTurn === 'b' && !isWhite)) {
            selected = {row: r, col: c};
            legalSquares = pseudoLegal(board, r, c, activeTurn);
            renderBoard(); return;
        }
    }

    selected = null; legalSquares = [];
    renderBoard();
}

// ═══════════════════════════════════════════════════════════
// Drag interaction
// ═══════════════════════════════════════════════════════════
let dragGhost = null;
let dragFrom  = null;

function setupDrag(el, r, c) {
    el.addEventListener('mousedown', e => startDrag(e, r, c));
    el.addEventListener('touchstart', e => startDrag(e, r, c), {passive:true});
}

function startDrag(e, r, c) {
    if (solved) return;
    const piece = board[r][c];
    if (!piece) return;
    const isWhite = piece === piece.toUpperCase();
    if ((activeTurn === 'w' && !isWhite) || (activeTurn === 'b' && isWhite)) return;

    dragFrom = {r, c};
    selected = {row: r, col: c};
    legalSquares = pseudoLegal(board, r, c, activeTurn);
    renderBoard();

    dragGhost = document.getElementById('drag-ghost');
    dragGhost.textContent = PIECE_UNICODE[piece];
    dragGhost.style.display = 'block';

    const mv = e.touches ? e.touches[0] : e;
    moveDragGhost(mv.clientX, mv.clientY);

    // Mark source piece as dragging
    setTimeout(() => {
        const srcPiece = document.querySelector(`.piece[data-r="${r}"][data-c="${c}"]`);
        if (srcPiece) srcPiece.classList.add('dragging');
    }, 0);

    document.addEventListener('mousemove', onDragMove);
    document.addEventListener('mouseup',   onDragEnd);
    document.addEventListener('touchmove', onDragMoveT, {passive:true});
    document.addEventListener('touchend',  onDragEndT);
}

function moveDragGhost(x, y) {
    dragGhost.style.left = x + 'px';
    dragGhost.style.top  = y + 'px';
}
function onDragMove(e)  { moveDragGhost(e.clientX, e.clientY); }
function onDragMoveT(e) { moveDragGhost(e.touches[0].clientX, e.touches[0].clientY); }

function onDragEnd(e)  { finishDrag(e.clientX, e.clientY); }
function onDragEndT(e) {
    const t = e.changedTouches[0];
    finishDrag(t.clientX, t.clientY);
}

function finishDrag(x, y) {
    dragGhost.style.display = 'none';
    document.removeEventListener('mousemove', onDragMove);
    document.removeEventListener('mouseup',   onDragEnd);
    document.removeEventListener('touchmove', onDragMoveT);
    document.removeEventListener('touchend',  onDragEndT);

    if (!dragFrom) return;

    // Find target square under cursor
    const el = document.elementFromPoint(x, y);
    const sq = el?.closest('.sq');
    if (sq) {
        const r2 = +sq.dataset.r;
        const c2 = +sq.dataset.c;
        if (legalSquares.some(s => s.row === r2 && s.col === c2)) {
            doMove(dragFrom.r, dragFrom.c, r2, c2);
            dragFrom = null; return;
        }
    }

    // Drop on illegal square — deselect
    selected = null; legalSquares = [];
    dragFrom = null;
    renderBoard();
}

// ═══════════════════════════════════════════════════════════
// Execute move
// ═══════════════════════════════════════════════════════════
function doMove(r1, c1, r2, c2) {
    const piece   = board[r1][c1];
    const capture = board[r2][c2];
    const uci     = moveToUCI(r1,c1,r2,c2);
    const san     = moveToSAN(piece, r1,c1,r2,c2, !!capture);

    board[r2][c2] = piece;
    board[r1][c1] = null;

    lastMove = {from:{r:r1,c:c1}, to:{r:r2,c:c2}};
    selected = null; legalSquares = [];
    moveHistory.push(san);
    updateMoveList();

    // Update progress bar
    document.getElementById('progress-fill').style.width = '60%';

    renderBoard();
    checkSolution(uci, san);
}

// ═══════════════════════════════════════════════════════════
// Check solution
// ═══════════════════════════════════════════════════════════
function checkSolution(uci, san) {
    fetch(`/puzzle/${PUZZLE_ID}/check`, {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN': CSRF_TOKEN},
        body: JSON.stringify({move: uci})
    })
    .then(r => {
        if (!r.ok) throw new Error('Server error: ' + r.status);
        return r.json();
    })
    .then(data => {
        if (data.correct) {
            flashBoard('correct');
            showFeedback('correct', `✓ Правильно! Отличный ход <strong>${san}</strong>`);
            document.getElementById('progress-fill').style.width = '100%';
            const btns = document.getElementById('action-btns');
            if (btns) btns.style.display = 'none';
            document.getElementById('solved-banner').classList.add('show');
            solved = true;
            setTimeout(() => {
                addAIMessage(`Отличная работа! Ход **${san}** — именно то, что нужно. ${getPositiveComment()}`, 'ai');
            }, 600);
        } else {
            flashBoard('wrong');
            showFeedback('wrong', `✗ Неверно. Попробуйте ещё раз!`);
            setTimeout(() => {
                board       = parseFEN(FEN_STR);
                moveHistory = [];
                lastMove    = null;
                updateMoveList();
                document.getElementById('progress-fill').style.width = '0%';
                renderBoard();
                hideFeedback();
            }, 900);
        }
    })
    .catch(err => {
        console.error('Ошибка проверки:', err);
        showFeedback('wrong', `Ошибка сервера. Проверь консоль.`);
        // Откат хода
        setTimeout(() => {
            board    = parseFEN(FEN_STR);
            lastMove = null;
            renderBoard();
            hideFeedback();
        }, 1200);
    });
}
// ═══════════════════════════════════════════════════════════
// UI helpers
// ═══════════════════════════════════════════════════════════
function flashBoard(type) {
    const el = document.getElementById('board-flash');
    el.className = `board-flash flash-${type} show`;
    setTimeout(() => el.classList.remove('show'), 400);
}

function showFeedback(type, html) {
    const el = document.getElementById('feedback-box');
    el.className = `feedback-box ${type}`;
    el.innerHTML = html;
}
function hideFeedback() {
    document.getElementById('feedback-box').className = 'feedback-box';
}

function updateMoveList() {
    const el = document.getElementById('moves-list');
    if (moveHistory.length === 0) {
        el.innerHTML = '<span style="font-size:0.78rem;color:var(--color-muted)">Ходов пока нет</span>';
        return;
    }
    el.innerHTML = moveHistory.map((m,i) =>
        `<span class="move-chip">${Math.floor(i/2)+1}${i%2===0?'.':'...'} ${m}</span>`
    ).join('');
}

function getPositiveComment() {
    const comments = [
        'Именно такие тактические паттерны встречаются в реальных партиях.',
        'Это классический пример тактики — запомни этот приём!',
        'Хорошее решение — продолжай в том же духе!',
        'Ты явно понимаешь структуру позиции.',
    ];
    return comments[Math.floor(Math.random() * comments.length)];
}

// ═══════════════════════════════════════════════════════════
// Hint
// ═══════════════════════════════════════════════════════════
function showHint() {
    if (hintUsed) return;
    hintUsed = true;
    document.getElementById('btn-hint').disabled = true;

    // Parse solution (UCI format like e2e4)
    const sol = SOLUTION.trim();
    if (sol.length >= 2) {
        const fc = FILES.indexOf(sol[0]);
        const fr = 8 - parseInt(sol[1]);
        // Highlight the source square
        const sq = document.querySelector(`.sq[data-r="${fr}"][data-c="${fc}"]`);
        if (sq) {
            sq.classList.add('hint-sq');
            setTimeout(() => sq.classList.remove('hint-sq'), 2500);
        }
        addAIMessage(`Подсказка: обрати внимание на фигуру на поле **${sol.substring(0,2).toUpperCase()}**. Куда она может пойти с максимальным эффектом?`, 'ai');
    }
}

// ═══════════════════════════════════════════════════════════
// Give up
// ═══════════════════════════════════════════════════════════
function giveUp() {
    if (!confirm('Показать решение?')) return;
    board = parseFEN(FEN_STR);
    renderBoard();
    showFeedback('wrong', `Правильный ход: <strong>${SOLUTION.toUpperCase()}</strong>`);
    document.getElementById('action-btns').style.display = 'none';
    addAIMessage(`Не расстраивайся! Правильный ход был **${SOLUTION.toUpperCase()}**. Давай разберём позицию, чтобы ты понял логику.`, 'ai');
    setTimeout(() => askAI('explain_solution'), 800);
}

// ═══════════════════════════════════════════════════════════
// Flip / Reset
// ═══════════════════════════════════════════════════════════
function flipBoard() {
    flipped = !flipped;
    renderBoard();
}

function resetBoard() {
    board       = parseFEN(FEN_STR);
    selected    = null;
    legalSquares = [];
    lastMove    = null;
    moveHistory = [];
    hintUsed    = false;
    solved      = false;

    updateMoveList();
    document.getElementById('progress-fill').style.width = '0%';
    hideFeedback();

    // Показать кнопки обратно
    const btns = document.getElementById('action-btns');
    if (btns) btns.style.display = 'flex';

    // Скрыть баннер победы
    document.getElementById('solved-banner').classList.remove('show');

    renderBoard();
}

// ═══════════════════════════════════════════════════════════
// AI Chat
// ═══════════════════════════════════════════════════════════
function addAIMessage(text, from) {
    const el = document.getElementById('ai-messages');
    const msg = document.createElement('div');
    msg.className = `msg from-${from}`;
    // Simple markdown bold
    msg.innerHTML = text
        .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
        .replace(/\n/g, '<br>');
    el.appendChild(msg);
    el.scrollTop = el.scrollHeight;
}

function showTyping() {
    const el = document.getElementById('ai-messages');
    const typing = document.createElement('div');
    typing.className = 'msg from-ai msg-typing';
    typing.id = 'typing-indicator';
    typing.innerHTML = '<span></span><span></span><span></span>';
    el.appendChild(typing);
    el.scrollTop = el.scrollHeight;
}
function hideTyping() {
    document.getElementById('typing-indicator')?.remove();
}

function sendAIMessage() {
    const input = document.getElementById('ai-input');
    const text  = input.value.trim();
    if (!text) return;
    input.value = '';
    addAIMessage(text, 'user');
    callAI(text);
}

function askAI(type) {
    let prompt;
    const solUpper = SOLUTION.toUpperCase();
    if (type === 'explain') {
        prompt = `Я решаю шахматную задачу "${@json($puzzle->title)}". FEN: ${FEN_STR}. Правильный ход — ${solUpper}. Объясни тактическую идею стоящую за этим ходом. Кратко (3-4 предложения), без спойлера самого хода.`;
    } else if (type === 'explain_solution') {
        prompt = `Объясни почему ход ${solUpper} является лучшим в позиции ${FEN_STR}. Название задачи: "${@json($puzzle->title)}". Объясни тактику 3-4 предложениями.`;
    }
    callAI(prompt);
}

function callAI(userMessage) {
    showTyping();
    const btn = document.getElementById('btn-ai');
    if (btn) btn.disabled = true;

    fetch('/ai/chat', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
        },
        body: JSON.stringify({
            system: 'Ты Гарри — дружелюбный ИИ-тренер по шахматам. Отвечай кратко, по-русски. Используй шахматную терминологию. Будь воодушевляющим и понятным. Максимум 4 предложения в ответе.',
            messages: [{ role: 'user', content: userMessage }]
        })
    })
    .then(r => r.json())
    .then(data => {
        hideTyping();
        const text = data.content?.[0]?.text || 'Не могу ответить прямо сейчас.';
        addAIMessage(text, 'ai');
    })
    .catch(() => {
        hideTyping();
        addAIMessage('Не удалось подключиться к тренеру. Попробуй ещё раз.', 'ai');
    })
    .finally(() => {
        if (btn) btn.disabled = false;
    });
}

// ═══════════════════════════════════════════════════════════
// Init
// ═══════════════════════════════════════════════════════════
board = parseFEN(FEN_STR);
renderBoard();

if (IS_SOLVED) {
    document.getElementById('solved-banner').classList.add('show');
}
</script>
@endpush