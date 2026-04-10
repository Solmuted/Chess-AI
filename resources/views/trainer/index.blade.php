@extends('layouts.app')
@section('title', 'Тренер Гарри')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/@chrisoakman/chessboardjs@1.0.0/dist/chessboard-1.0.0.min.css">
<style>
:root { --board-size: 480px; }

.trainer-page { max-width: 1300px; margin: 0 auto; padding: 1.5rem 1rem 3rem; }

.color-picker {
    display: flex; gap: 1rem; margin-bottom: 2rem; justify-content: center;
}
.color-btn {
    display: flex; align-items: center; gap: 0.75rem;
    padding: 1rem 2rem; border-radius: 16px;
    border: 0.5px solid var(--color-border);
    background: var(--color-surface);
    cursor: pointer; font-size: 1rem; font-weight: 500;
    text-decoration: none; color: var(--color-text);
    transition: border-color 0.15s, transform 0.15s;
}
.color-btn:hover { border-color: var(--color-muted); transform: translateY(-1px); }
.color-btn.active-white { border-color: #4f46e5; background: rgba(79,70,229,0.07); }
.color-btn.active-black { border-color: var(--color-muted); background: #1a1a2e; color: #f0efe8; }
.piece-icon { font-size: 2rem; line-height: 1; }

.trainer-layout {
    display: grid;
    grid-template-columns: var(--board-size) 1fr;
    gap: 1.5rem; align-items: start;
}

.board-card {
    background: var(--color-surface);
    border: 0.5px solid var(--color-border);
    border-radius: 20px; padding: 1.5rem;
}
.board-title {
    display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;
}
.board-title h2 { font-size: 1.1rem; font-weight: 500; color: var(--color-text); margin: 0; }

.playing-as {
    font-size: 0.78rem; padding: 4px 12px; border-radius: 20px; font-weight: 500;
}
.playing-white {
    background: var(--color-bg);
    border: 0.5px solid var(--color-border);
    color: var(--color-muted);
}
.playing-black { background: #1a1a2e; color: #f0efe8; }

#board { width: var(--board-size) !important; }

.status-bar {
    display: flex; justify-content: space-between; align-items: center;
    margin-top: 1rem; padding: 0.75rem 1rem;
    background: var(--color-bg);
    border: 0.5px solid var(--color-border);
    border-radius: 10px; font-size: 0.875rem; color: var(--color-text);
}
.turn-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 6px; }
.dot-white { background: #f1f5f9; border: 2px solid #94a3b8; }
.dot-black { background: #1a1a2e; }
.move-count { font-size: 0.8rem; color: var(--color-muted); }

.board-controls { display: flex; gap: 0.5rem; margin-top: 1rem; flex-wrap: wrap; }
.btn {
    padding: 0.5rem 1rem; border-radius: 8px; border: 0.5px solid var(--color-border);
    cursor: pointer; font-size: 0.83rem; font-weight: 500;
    font-family: inherit; transition: all 0.15s;
    background: var(--color-bg); color: var(--color-text);
}
.btn:disabled { opacity: 0.4; cursor: not-allowed; }
.btn:hover:not(:disabled) { border-color: var(--color-muted); }
.btn-end  { color: #4f46e5; border-color: rgba(79,70,229,0.3); }
.btn-end:hover:not(:disabled)  { background: rgba(79,70,229,0.07); }
.btn-new  { color: #a32d2d; border-color: rgba(163,45,45,0.3); }
.btn-new:hover:not(:disabled)  { background: rgba(163,45,45,0.07); }

.board-wrap { position: relative; }
.thinking-overlay {
    display: none; position: absolute; inset: 0;
    background: rgba(0,0,0,0.35); border-radius: 4px;
    align-items: center; justify-content: center;
    font-size: 0.95rem; font-weight: 500; color: white; z-index: 10;
    flex-direction: column; gap: 0.5rem;
}
.thinking-overlay.show { display: flex; }
.spinner {
    width: 28px; height: 28px; border: 3px solid rgba(255,255,255,0.2);
    border-top-color: white; border-radius: 50%; animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

.chat-card {
    background: var(--color-surface);
    border: 0.5px solid var(--color-border);
    border-radius: 20px;
    display: flex; flex-direction: column; height: 580px;
}
.chat-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 0.5px solid var(--color-border);
    display: flex; align-items: center; gap: 1rem;
}
.trainer-avatar {
    width: 44px; height: 44px; border-radius: 50%;
    background: #4f46e5;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem; flex-shrink: 0; color: white;
}
.chat-header-info h2 { font-size: 1rem; font-weight: 500; color: var(--color-text); margin: 0; }
.chat-header-info p  { font-size: 0.78rem; color: var(--color-muted); margin: 0; }
.online-dot { width: 7px; height: 7px; background: #22c55e; border-radius: 50%; display: inline-block; margin-right: 4px; }

.chat-messages {
    flex: 1; overflow-y: auto; padding: 1rem;
    display: flex; flex-direction: column; gap: 0.75rem;
}
.msg-wrap-user      { display: flex; justify-content: flex-end; }
.msg-wrap-assistant { display: flex; justify-content: flex-start; gap: 0.5rem; align-items: flex-end; }
.msg-avatar {
    width: 28px; height: 28px; border-radius: 50%;
    background: #4f46e5; display: flex; align-items: center;
    justify-content: center; font-size: 0.75rem; color: white; flex-shrink: 0;
}
.bubble-user {
    background: #4f46e5; color: white;
    padding: 0.625rem 1rem; border-radius: 18px 18px 4px 18px;
    font-size: 0.875rem; line-height: 1.6;
}
.bubble-assistant {
    background: var(--color-bg);
    border: 0.5px solid var(--color-border);
    color: var(--color-text);
    padding: 0.625rem 1rem; border-radius: 18px 18px 18px 4px;
    font-size: 0.875rem; line-height: 1.6;
}

.typing-indicator {
    display: none; align-items: center; gap: 4px; padding: 0.625rem 1rem;
    background: var(--color-bg);
    border: 0.5px solid var(--color-border);
    border-radius: 18px; width: fit-content;
}
.typing-indicator.show { display: flex; }
.typing-dot {
    width: 6px; height: 6px; border-radius: 50%; background: var(--color-muted);
    animation: bounce 1.2s infinite;
}
.typing-dot:nth-child(2) { animation-delay: 0.2s; }
.typing-dot:nth-child(3) { animation-delay: 0.4s; }
@keyframes bounce { 0%,60%,100% { transform: translateY(0); } 30% { transform: translateY(-6px); } }

.chat-input-area {
    padding: 1rem; border-top: 0.5px solid var(--color-border);
    display: flex; gap: 0.5rem;
}
.chat-input-area input {
    flex: 1; padding: 0.625rem 1rem;
    border: 0.5px solid var(--color-border);
    border-radius: 24px; font-size: 0.875rem; outline: none;
    background: var(--color-bg); color: var(--color-text);
    font-family: inherit;
    transition: border-color 0.15s;
}
.chat-input-area input:focus { border-color: #4f46e5; }
.chat-input-area input::placeholder { color: var(--color-muted); }
.send-btn {
    width: 40px; height: 40px; border-radius: 50%; background: #4f46e5;
    border: none; cursor: pointer; display: flex; align-items: center;
    justify-content: center; flex-shrink: 0; transition: background 0.15s;
}
.send-btn:hover { background: #4338ca; }
.send-btn svg { width: 16px; height: 16px; fill: white; }

.analysis-panel {
    display: none; margin-top: 1.5rem;
    background: var(--color-surface);
    border: 0.5px solid var(--color-border);
    border-radius: 20px; overflow: hidden;
}
.analysis-panel.show { display: block; }
.analysis-header {
    padding: 1.25rem 1.5rem;
    background: var(--color-bg);
    border-bottom: 0.5px solid var(--color-border);
    display: flex; align-items: center; gap: 0.75rem;
}
.analysis-header h3 { margin: 0; font-size: 1rem; font-weight: 500; color: var(--color-text); }
.analysis-grid {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 0; border-bottom: 0.5px solid var(--color-border);
}
.analysis-block {
    padding: 1.25rem 1.5rem;
    border-right: 0.5px solid var(--color-border);
}
.analysis-block:last-child { border-right: none; }
.analysis-block-title {
    font-size: 0.78rem; font-weight: 500; letter-spacing: 0.04em;
    text-transform: uppercase; margin-bottom: 0.5rem;
}
.analysis-block-body { font-size: 0.875rem; line-height: 1.6; color: var(--color-muted); white-space: pre-line; }
.ab-best   .analysis-block-title { color: #38a169; }
.ab-errors .analysis-block-title { color: #e53e3e; }
.ab-blunder .analysis-block-title { color: #d97706; }
.ab-tip    .analysis-block-title { color: #4f46e5; }
.analysis-raw { padding: 1.25rem 1.5rem; font-size: 0.875rem; line-height: 1.7; color: var(--color-text); white-space: pre-line; }

@media(max-width: 1000px) {
    .trainer-layout { grid-template-columns: 1fr; }
    :root { --board-size: 340px; }
    .chat-card { height: 420px; }
    .color-btn { padding: 0.75rem 1.25rem; font-size: 0.9rem; }
}
</style>
@endpush

@section('content')
<div class="trainer-page">

    @if(!isset($color) || request()->has('pick'))
    <div class="color-picker" id="colorPicker">
        <a href="/trainer?color=white" class="color-btn {{ $color === 'white' ? 'active-white' : '' }}">
            <span class="piece-icon">♔</span>
            <div>
                <div>Играть белыми</div>
                <div style="font-size:0.75rem;opacity:0.55;font-weight:400">Ты ходишь первым</div>
            </div>
        </a>
        <a href="/trainer?color=black" class="color-btn {{ $color === 'black' ? 'active-black' : '' }}">
            <span class="piece-icon">♚</span>
            <div>
                <div>Играть чёрными</div>
                <div style="font-size:0.75rem;opacity:0.55;font-weight:400">Гарри ходит первым</div>
            </div>
        </a>
    </div>
    @endif

    <div class="trainer-layout">
        <div class="board-col">
            <div class="board-card">
                <div class="board-title">
                    <h2>Партия с тренером</h2>
                    <span class="playing-as {{ $color === 'white' ? 'playing-white' : 'playing-black' }}">
                        {{ $color === 'white' ? '♔ Белые' : '♚ Чёрные' }}
                    </span>
                </div>
                <div class="board-wrap">
                    <div id="board"></div>
                    <div class="thinking-overlay" id="thinkingOverlay">
                        <div class="spinner"></div>
                        <span>Гарри думает...</span>
                    </div>
                </div>
                <div class="status-bar">
                    <div id="statusText">
                        <span class="turn-dot dot-white" id="turnDot"></span>
                        <span id="statusLabel">Твой ход</span>
                    </div>
                    <span class="move-count" id="moveCount">Ход 1</span>
                </div>
                <div class="board-controls">
                    <button class="btn" onclick="flipBoard()">↕ Перевернуть</button>
                    <button class="btn" onclick="undoMove()" id="btnUndo">↩ Отменить</button>
                    <a href="/trainer/setup" class="btn btn-new">✕ Новая партия</a>
                    <button class="btn btn-end" onclick="endGame()" id="btnEnd">📊 Разобрать партию</button>
                </div>
            </div>
        </div>

        <div class="chat-col">
            <div class="chat-card">
                <div class="chat-header">
                    <div class="trainer-avatar">♟</div>
                    <div class="chat-header-info">
                        <h2>Тренер Гарри</h2>
                        <p><span class="online-dot"></span>Уровень {{ $user->level }}/5 · Рейтинг {{ $user->rating }}</p>
                    </div>
                </div>
                <div class="chat-messages" id="chatMessages">
                    @foreach($messages as $msg)
                        <div class="msg-wrap-{{ $msg->role }}">
                            @if($msg->role === 'assistant')
                                <div class="msg-avatar">Г</div>
                            @endif
                            <div><div class="bubble-{{ $msg->role }}">{{ $msg->content }}</div></div>
                        </div>
                    @endforeach
                    @if($messages->isEmpty())
                        <div class="msg-wrap-assistant">
                            <div class="msg-avatar">Г</div>
                            <div>
                                <div class="bubble-assistant">
                                    Привет, {{ $user->name }}! Я тренер Гарри.
                                    @if($color === 'white') Ты играешь белыми — делай первый ход!
                                    @else Ты играешь чёрными — я начинаю!
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="msg-wrap-assistant" id="typingWrap" style="display:none">
                        <div class="msg-avatar">Г</div>
                        <div class="typing-indicator show" id="typing">
                            <div class="typing-dot"></div>
                            <div class="typing-dot"></div>
                            <div class="typing-dot"></div>
                        </div>
                    </div>
                </div>
                <div class="chat-input-area">
                    <input type="text" id="chatInput" placeholder="Спроси тренера..."
                           onkeypress="if(event.key==='Enter') sendMessage()">
                    <button class="send-btn" onclick="sendMessage()">
                        <svg viewBox="0 0 24 24"><path d="M2 21l21-9L2 3v7l15 2-15 2v7z"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="analysis-panel" id="analysisPanel">
        <div class="analysis-header">
            <span style="font-size:1.4rem">📊</span>
            <h3>Разбор партии от тренера Гарри</h3>
        </div>
        <div class="analysis-grid" id="analysisGrid"></div>
        <div class="analysis-raw" id="analysisRaw"></div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://unpkg.com/@chrisoakman/chessboardjs@1.0.0/dist/chessboard-1.0.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chess.js/0.10.3/chess.min.js"></script>
<script>
const SESSION_ID   = {{ $session->id }};
const CSRF         = document.querySelector('meta[name="csrf-token"]').content;
const PLAYER_COLOR = '{{ $color }}';
const AI_COLOR     = PLAYER_COLOR === 'white' ? 'b' : 'w';

let game       = new Chess();
let board;
let playerTurn = PLAYER_COLOR === 'white';

board = Chessboard('board', {
    draggable:   true,
    position:    'start',
    orientation: PLAYER_COLOR,
    onDrop:      onDrop,
    onSnapEnd:   () => board.position(game.fen()),
    onDragStart: (source, piece) => {
        if (!playerTurn || game.game_over()) return false;
        if (PLAYER_COLOR === 'white' && piece.search(/^b/) !== -1) return false;
        if (PLAYER_COLOR === 'black' && piece.search(/^w/) !== -1) return false;
    },
	    pieceTheme: function(piece) {
	    const style = '{{ $user->piece_style ?? "cburnett" }}';
	    return 'https://lichess1.org/assets/piece/' + style + '/' + piece + '.svg';
	},
});

if (PLAYER_COLOR === 'black') setTimeout(() => makeTrainerMove(), 500);

function onDrop(source, target) {
    const move = game.move({ from: source, to: target, promotion: 'q' });
    if (move === null) return 'snapback';
    playerTurn = false;
    updateStatus();
    askTrainerAndMove(move.san);
}

function askTrainerAndMove(moveSan) {
    showTyping(true);
    fetch('/trainer/chat', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ message: `Я сделал ход ${moveSan}. Коротко прокомментируй.`, fen: game.fen(), pgn: game.pgn(), session_id: SESSION_ID }),
    })
    .then(r => r.json())
    .then(data => {
        showTyping(false);
        appendMessage('assistant', data.reply);
        if (game.game_over()) { handleGameOver(); return; }
        makeTrainerMove();
    })
    .catch(() => { showTyping(false); if (!game.game_over()) makeTrainerMove(); });
}

function makeTrainerMove() {
    document.getElementById('thinkingOverlay').classList.add('show');
    fetch('/trainer/move', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ fen: game.fen(), pgn: game.pgn(), session_id: SESSION_ID, playing_as: PLAYER_COLOR }),
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('thinkingOverlay').classList.remove('show');
        if (data.move && data.move.length >= 4) {
            const from = data.move.substring(0,2), to = data.move.substring(2,4);
            const promo = data.move.length === 5 ? data.move[4] : 'q';
            const move = game.move({ from, to, promotion: promo });
            if (move) { board.position(game.fen()); playerTurn = true; updateStatus(); if (game.game_over()) handleGameOver(); return; }
        }
        fallbackMove();
    })
    .catch(() => { document.getElementById('thinkingOverlay').classList.remove('show'); fallbackMove(); });
}

function fallbackMove() {
    const moves = game.moves();
    if (moves.length > 0) {
        game.move(moves[Math.floor(Math.random() * moves.length)]);
        board.position(game.fen()); playerTurn = true; updateStatus();
    }
}

function handleGameOver() {
    let result = game.in_checkmate() ? (game.turn() === 'w' ? '🏆 Ты победил!' : '😔 Гарри победил') : '🤝 Ничья!';
    document.getElementById('statusLabel').textContent = result;
    appendMessage('assistant', `Партия завершена: ${result} Нажми "📊 Разобрать партию" для анализа!`);
    playerTurn = false;
}

function updateStatus() {
    const dot = document.getElementById('turnDot'), label = document.getElementById('statusLabel');
    document.getElementById('moveCount').textContent = `Ход ${Math.ceil(game.history().length / 2) + 1}`;
    if (game.game_over()) return;
    if (playerTurn) {
        dot.className = PLAYER_COLOR === 'white' ? 'turn-dot dot-white' : 'turn-dot dot-black';
        label.textContent = game.in_check() ? '⚠️ Твой ход — ШАХ!' : 'Твой ход';
    } else {
        dot.className = PLAYER_COLOR === 'white' ? 'turn-dot dot-black' : 'turn-dot dot-white';
        label.textContent = 'Гарри думает...';
    }
}

function endGame() {
    if (game.history().length < 2) { appendMessage('assistant', 'Сыграй хотя бы несколько ходов перед разбором!'); return; }
    document.getElementById('analysisPanel').classList.add('show');
    document.getElementById('analysisRaw').textContent = 'Анализирую партию...';
    document.getElementById('analysisGrid').innerHTML = '';
    fetch('/trainer/analyze-game', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ pgn: game.pgn(), fen: game.fen(), session_id: SESSION_ID }),
    })
    .then(r => r.json())
    .then(data => { renderAnalysis(data.analysis); appendMessage('assistant', '📊 Разбор готов — смотри панель ниже!'); document.getElementById('analysisPanel').scrollIntoView({ behavior: 'smooth' }); })
    .catch(() => { document.getElementById('analysisRaw').textContent = 'Не удалось получить анализ.'; });
}

function renderAnalysis(text) {
    const sections = [
        { key: '✅',  cls: 'ab-best',    title: '✅ Лучшие ходы' },
        { key: '❌',  cls: 'ab-errors',  title: '❌ Ошибки' },
        { key: '⚠️', cls: 'ab-blunder', title: '⚠️ Зевки' },
        { key: '💡',  cls: 'ab-tip',     title: '💡 Совет' },
    ];
    const grid = document.getElementById('analysisGrid'), raw = document.getElementById('analysisRaw');
    grid.innerHTML = '';
    let found = false;
    sections.forEach(s => {
        const idx = text.indexOf(s.key);
        if (idx === -1) return;
        found = true;
        let end = text.length;
        sections.forEach(s2 => { if (s2.key === s.key) return; const i2 = text.indexOf(s2.key); if (i2 > idx && i2 < end) end = i2; });
        const content = text.slice(idx + s.key.length, end).trim();
        grid.innerHTML += `<div class="analysis-block ${s.cls}"><div class="analysis-block-title">${s.title}</div><div class="analysis-block-body">${content}</div></div>`;
    });
    raw.textContent = found ? '' : text;
}

function sendMessage() {
    const input = document.getElementById('chatInput'), msg = input.value.trim();
    if (!msg) return;
    input.value = '';
    appendMessage('user', msg);
    showTyping(true);
    fetch('/trainer/chat', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ message: msg, fen: game.fen(), pgn: game.pgn(), session_id: SESSION_ID }),
    })
    .then(r => r.json())
    .then(data => { showTyping(false); appendMessage('assistant', data.reply); })
    .catch(() => { showTyping(false); });
}

function appendMessage(role, text) {
    const chat = document.getElementById('chatMessages');
    const wrap = document.createElement('div');
    wrap.className = `msg-wrap-${role}`;
    wrap.innerHTML = role === 'assistant'
        ? `<div class="msg-avatar">Г</div><div><div class="bubble-assistant">${text}</div></div>`
        : `<div><div class="bubble-user">${text}</div></div>`;
    chat.insertBefore(wrap, document.getElementById('typingWrap'));
    scrollChat();
}

function showTyping(show) { document.getElementById('typingWrap').style.display = show ? 'flex' : 'none'; scrollChat(); }
function scrollChat() { const c = document.getElementById('chatMessages'); c.scrollTop = c.scrollHeight; }
function flipBoard() { board.flip(); }
function undoMove() { if (!playerTurn) return; game.undo(); game.undo(); board.position(game.fen()); updateStatus(); }

scrollChat();
</script>
@endpush

