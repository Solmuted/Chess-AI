@extends('layouts.app')
@section('title', $lesson->title)

@push('styles')
<style>
.ls-wrap { max-width: 760px; margin: 0 auto; padding: 1.5rem 1rem 3rem; }
.ls-back { font-size:0.8rem; color:var(--color-muted); text-decoration:none; display:inline-flex; align-items:center; gap:5px; margin-bottom:1.2rem; transition:color .15s; }
.ls-back:hover { color:var(--color-text); }

/* Progress bar top */
.ls-progress { height:3px; background:var(--color-border); border-radius:0; margin-bottom:1.5rem; }
.ls-progress-fill { height:100%; background:#5a7fe8; border-radius:0; transition:width .4s; }

/* Header */
.ls-header { margin-bottom:1.5rem; }
.ls-breadcrumb { font-size:0.76rem; color:var(--color-muted); margin-bottom:6px; }
.ls-breadcrumb a { color:var(--color-muted); text-decoration:none; }
.ls-breadcrumb a:hover { color:var(--color-text); }
.ls-title { font-size:1.4rem; font-weight:600; color:var(--color-text); margin-bottom:6px; font-family:Georgia,serif; }
.ls-meta { font-size:0.78rem; color:var(--color-muted); display:flex; gap:12px; flex-wrap:wrap; }
.ls-topic-badge { padding:2px 10px; border-radius:20px; font-size:0.72rem; font-weight:500; background:rgba(90,127,232,0.1); color:#5a7fe8; }

/* Content card */
.ls-content {
    background:var(--color-surface);
    border:0.5px solid var(--color-border);
    border-radius:16px; padding:28px;
    margin-bottom:16px; line-height:1.75;
    font-size:0.92rem; color:var(--color-text);
}
.ls-content h2 { font-size:1.1rem; font-weight:600; margin:20px 0 8px; color:var(--color-text); }
.ls-content h3 { font-size:0.95rem; font-weight:600; margin:16px 0 6px; color:var(--color-text); }
.ls-content p  { margin-bottom:12px; }
.ls-content ul, .ls-content ol { margin:8px 0 12px 20px; }
.ls-content li { margin-bottom:4px; }
.ls-content strong { color:var(--color-text); font-weight:600; }
.ls-content .highlight {
    background:rgba(90,127,232,0.08);
    border-left:3px solid #5a7fe8;
    padding:12px 16px; border-radius:0 8px 8px 0;
    margin:16px 0; font-size:0.88rem;
}
.ls-content .piece-example {
    display:inline-flex; align-items:center; gap:6px;
    background:var(--color-bg); border:0.5px solid var(--color-border);
    border-radius:8px; padding:4px 10px; font-size:0.85rem; margin:2px;
}

/* AI explanation panel */
.ai-panel {
    background:var(--color-surface);
    border:0.5px solid var(--color-border);
    border-radius:14px; overflow:hidden; margin-bottom:16px;
}
.ai-panel-header {
    display:flex; align-items:center; gap:10px; padding:14px 18px;
    border-bottom:0.5px solid var(--color-border); cursor:pointer;
    transition:background .15s;
}
.ai-panel-header:hover { background:var(--color-bg); }
.ai-avatar { width:28px; height:28px; border-radius:7px; background:linear-gradient(135deg,#5a7fe8,#9b59e8); display:flex; align-items:center; justify-content:center; font-size:13px; flex-shrink:0; }
.ai-panel-title { font-size:0.85rem; font-weight:500; color:var(--color-text); flex:1; }
.ai-panel-toggle { font-size:0.8rem; color:var(--color-muted); }
.ai-body { padding:16px 18px; display:none; }
.ai-body.open { display:block; }
.ai-text { font-size:0.84rem; line-height:1.65; color:var(--color-text); }
.ai-loading { display:flex; gap:4px; align-items:center; padding:4px 0; }
.ai-loading span { width:6px; height:6px; border-radius:50%; background:var(--color-muted); animation:blink 1.2s infinite; }
.ai-loading span:nth-child(2) { animation-delay:.2s; }
.ai-loading span:nth-child(3) { animation-delay:.4s; }
@keyframes blink { 0%,80%,100%{opacity:.2} 40%{opacity:1} }

/* Navigation */
.ls-nav {
    display:flex; gap:10px; align-items:center; margin-top:8px;
}
.btn-complete {
    flex:1; padding:12px 20px; background:#5a7fe8; color:#fff;
    border:none; border-radius:12px; font-size:0.9rem; font-weight:500;
    cursor:pointer; font-family:inherit; transition:background .15s, transform .15s;
    display:flex; align-items:center; justify-content:center; gap:8px;
}
.btn-complete:hover { background:#4a6fd4; transform:translateY(-1px); }
.btn-complete.done { background:#3dba82; cursor:default; transform:none; }
.btn-nav {
    padding:12px 18px; background:var(--color-surface);
    border:0.5px solid var(--color-border); border-radius:12px;
    font-size:0.85rem; color:var(--color-muted); text-decoration:none;
    transition:all .15s; white-space:nowrap;
}
.btn-nav:hover { border-color:var(--color-muted); color:var(--color-text); }
</style>
@endpush

@section('content')
<div class="ls-wrap">
    {{-- Progress bar --}}
    @if($lesson->course)
    <div class="ls-progress">
        <div class="ls-progress-fill" style="width:{{ $courseProgress }}%"></div>
    </div>
    @endif

    <a href="{{ $lesson->course_id ? '/course/'.$lesson->course_id : '/courses' }}" class="ls-back">
        ← {{ $lesson->course->title ?? 'Курсы' }}
    </a>

    <div class="ls-header">
        @if($lesson->course)
        <div class="ls-breadcrumb">
            <a href="/courses">Курсы</a> / <a href="/course/{{ $lesson->course_id }}">{{ $lesson->course->title }}</a>
        </div>
        @endif
        <div class="ls-title">{{ $lesson->title }}</div>
        <div class="ls-meta">
            <span>Урок {{ $lesson->order_in_course }}</span>
            @php
                $topicLabels = ['basics'=>'Основы','opening'=>'Дебют','tactics'=>'Тактика','endgame'=>'Эндшпиль','strategy'=>'Стратегия'];
                $topicIcons  = ['basics'=>'♟','opening'=>'🏰','tactics'=>'⚔','endgame'=>'👑','strategy'=>'🧠'];
            @endphp
            <span class="ls-topic-badge">
                {{ $topicIcons[$lesson->topic] ?? '♟' }} {{ $topicLabels[$lesson->topic] ?? $lesson->topic }}
            </span>
            @if($userLesson->status === 'completed')
                <span style="color:#3dba82">✓ Пройден</span>
            @endif
        </div>
    </div>

    {{-- Lesson content (AI-generated dynamically) --}}
    <div class="ls-content" id="lesson-content">
        <div class="ai-loading" id="content-loading">
            <span></span><span></span><span></span>
            <span style="font-size:0.82rem;color:var(--color-muted);margin-left:6px">Загружаем материал урока...</span>
        </div>
        <div id="lesson-text" style="display:none"></div>
    </div>

    {{-- AI Ask panel --}}
    <div class="ai-panel">
        <div class="ai-panel-header" onclick="toggleAI()">
            <div class="ai-avatar">♟</div>
            <span class="ai-panel-title">Гарри объясняет — задай вопрос</span>
            <span class="ai-panel-toggle" id="ai-toggle">▼ Открыть</span>
        </div>
        <div class="ai-body" id="ai-body">
            <div id="ai-messages" style="display:flex;flex-direction:column;gap:10px;margin-bottom:12px"></div>
            <div style="display:flex;gap:8px">
                <input id="ai-input" placeholder="Задай вопрос по теме урока..."
                    style="flex:1;padding:8px 12px;border-radius:8px;border:0.5px solid var(--color-border);background:var(--color-bg);color:var(--color-text);font-size:0.82rem;font-family:inherit;outline:none"
                    onkeydown="if(event.key==='Enter')askQuestion()">
                <button onclick="askQuestion()"
                    style="padding:8px 14px;border-radius:8px;background:#5a7fe8;color:#fff;border:none;cursor:pointer;font-family:inherit;font-size:0.82rem">→</button>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <div class="ls-nav">
        @if($prev)
            <a href="/lesson/{{ $prev->id }}" class="btn-nav">← Пред.</a>
        @endif

        <button class="btn-complete {{ $userLesson->status === 'completed' ? 'done' : '' }}"
                id="btn-complete"
                onclick="completeLesson()"
                {{ $userLesson->status === 'completed' ? 'disabled' : '' }}>
            {{ $userLesson->status === 'completed' ? '✓ Урок пройден' : '✓ Отметить как пройденный' }}
        </button>

        @if($next)
            <a href="/lesson/{{ $next->id }}" class="btn-nav" id="btn-next">След. →</a>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
const LESSON_TITLE  = @json($lesson->title);
const LESSON_TOPIC  = @json($lesson->topic);
const LESSON_DESC   = @json($lesson->description ?? '');
const LESSON_ID     = {{ $lesson->id }};
const CSRF_TOKEN    = document.querySelector('meta[name="csrf-token"]').content;
const IS_COMPLETED  = @json($userLesson->status === 'completed');

// ── Load lesson content via AI ──────────────────────────────
async function loadLessonContent() {
    try {
        const response = await fetch('/ai/chat', {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN': CSRF_TOKEN},
            body: JSON.stringify({
                system: 'Ты Гарри — ИИ-тренер по шахматам. Создавай структурированные, понятные и увлекательные уроки по шахматам на русском языке. Используй конкретные примеры, объяснения и советы. Отвечай в HTML формате используя теги h2, h3, p, ul, li, strong. Добавляй div class="highlight" для важных советов. Уроки должны быть подробными (300-500 слов).',
                messages: [{
                    role: 'user',
                    content: `Напиши подробный урок на тему: "${LESSON_TITLE}". Описание: ${LESSON_DESC}. Тема: ${LESSON_TOPIC}. Включи: объяснение концепции, конкретные примеры с шахматными позициями (используй текстовое описание), практические советы, распространённые ошибки. Отвечай только HTML контентом без обёртки.`
                }]
            })
        });
        const data = await response.json();
        const text = data.content?.[0]?.text || 'Не удалось загрузить содержимое урока.';
        document.getElementById('content-loading').style.display = 'none';
        const el = document.getElementById('lesson-text');
        el.innerHTML = text;
        el.style.display = 'block';
    } catch(e) {
        document.getElementById('content-loading').innerHTML =
            '<p style="color:var(--color-muted);font-size:0.85rem">Не удалось загрузить урок. Проверьте подключение.</p>';
    }
}

// ── AI Q&A ──────────────────────────────────────────────────
let aiOpen = false;
function toggleAI() {
    aiOpen = !aiOpen;
    document.getElementById('ai-body').classList.toggle('open', aiOpen);
    document.getElementById('ai-toggle').textContent = aiOpen ? '▲ Закрыть' : '▼ Открыть';
}

function addMsg(text, from) {
    const el = document.getElementById('ai-messages');
    const d  = document.createElement('div');
    d.style.cssText = `font-size:0.82rem;line-height:1.6;padding:10px 12px;border-radius:${from==='ai'?'0 10px 10px 10px':'10px 0 10px 10px'};background:${from==='ai'?'var(--color-bg)':'rgba(90,127,232,0.1)'};border:0.5px solid ${from==='ai'?'var(--color-border)':'rgba(90,127,232,0.2)'};${from==='user'?'align-self:flex-end;max-width:85%':''}`;
    d.innerHTML = text.replace(/\n/g,'<br>');
    el.appendChild(d);
    el.scrollTop = el.scrollHeight;
    return d;
}

async function askQuestion() {
    const input = document.getElementById('ai-input');
    const q = input.value.trim();
    if (!q) return;
    input.value = '';
    addMsg(q, 'user');

    const typing = addMsg('<span style="display:flex;gap:4px"><span style="width:6px;height:6px;border-radius:50%;background:var(--color-muted);animation:blink 1.2s infinite"></span><span style="width:6px;height:6px;border-radius:50%;background:var(--color-muted);animation:blink 1.2s .2s infinite"></span><span style="width:6px;height:6px;border-radius:50%;background:var(--color-muted);animation:blink 1.2s .4s infinite"></span></span>', 'ai');

    try {
        const r = await fetch('/ai/chat', {
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN},
            body: JSON.stringify({
                system: `Ты Гарри — дружелюбный ИИ-тренер по шахматам. Студент проходит урок "${LESSON_TITLE}" (тема: ${LESSON_TOPIC}). Отвечай кратко и по делу, по-русски. Максимум 4 предложения.`,
                messages: [{role:'user',content:q}]
            })
        });
        const data = await r.json();
        typing.innerHTML = data.content?.[0]?.text || 'Не могу ответить.';
    } catch {
        typing.innerHTML = 'Ошибка соединения.';
    }
}

// ── Complete lesson ──────────────────────────────────────────
async function completeLesson() {
    if (IS_COMPLETED) return;
    const btn = document.getElementById('btn-complete');
    btn.disabled = true;
    btn.textContent = 'Сохраняем...';

    try {
        const r = await fetch(`/lesson/${LESSON_ID}/complete`, {
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN},
            body: JSON.stringify({})
        });
        const data = await r.json();
        if (data.success) {
            btn.textContent = '✓ Урок пройден! +3 к рейтингу';
            btn.className = 'btn-complete done';
            // Подсветить кнопку "Следующий"
            const nextBtn = document.getElementById('btn-next');
            if (nextBtn) {
                nextBtn.style.background = '#5a7fe8';
                nextBtn.style.color = '#fff';
                nextBtn.style.borderColor = '#5a7fe8';
            }
        }
    } catch {
        btn.textContent = 'Ошибка. Попробуй ещё раз.';
        btn.disabled = false;
    }
}

// Init
loadLessonContent();
</script>
@endpush
