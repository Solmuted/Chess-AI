<!DOCTYPE html>
<html lang="ru" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ИИ-тренер по шахматам')</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
	<link rel="alternate icon" href="/favicon.ico">
    @vite(['resources/css/app.css'])
    <style>
        :root {
            --nav-accent: #f0c040;
            --nav-h: 56px;
        }
        [data-theme="dark"] {
            --color-bg: #0f0f13;
            --color-surface: #1a1a24;
            --color-border: rgba(255,255,255,0.08);
            --color-text: #f0efe8;
            --color-muted: #888780;
        }
        [data-theme="light"] {
            --color-bg: #f5ede0;
            --color-surface: #fdf6ee;
            --color-border: rgba(0,0,0,0.08);
            --color-text: #1a1a18;
            --color-muted: #5f5e5a;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: system-ui, sans-serif;
            background: var(--color-bg);
            color: var(--color-text);
            min-height: 100vh;
            display: flex; flex-direction: column;
            transition: background 0.2s, color 0.2s;
        }

        /* ── Nav ── */
        nav {
            height: var(--nav-h);
            background: var(--color-surface);
            border-bottom: 0.5px solid var(--color-border);
            display: flex; align-items: center;
            padding: 0 20px; gap: 0;
            position: sticky; top: 0; z-index: 100;
        }
        .nav-brand {
            display: flex; align-items: center; gap: 8px;
            font-size: 16px; font-weight: 500;
            color: var(--color-text); text-decoration: none;
            margin-right: 28px; flex-shrink: 0;
        }
        .nav-brand-icon {
            width: 30px; height: 30px;
            background: var(--nav-accent); border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px;
        }
        .nav-links { display: flex; align-items: center; gap: 1px; flex: 1; }
        .nav-link {
            display: flex; align-items: center; gap: 6px;
            padding: 6px 11px; border-radius: 8px;
            font-size: 13.5px; color: var(--color-muted);
            text-decoration: none; white-space: nowrap;
            transition: background 0.15s, color 0.15s;
            position: relative;
        }
        .nav-link:hover { background: var(--color-bg); color: var(--color-text); }
        .nav-link.active { color: var(--color-text); font-weight: 500; }
        .nav-link.active::after {
            content: '';
            position: absolute; bottom: -9px;
            left: 11px; right: 11px;
            height: 2px; background: #4f46e5; border-radius: 1px;
        }
        .nav-link svg { width: 15px; height: 15px; flex-shrink: 0; }

        /* Nav right */
        .nav-right { display: flex; align-items: center; gap: 6px; margin-left: auto; }

        .level-pill {
            font-size: 11.5px; padding: 3px 9px; border-radius: 20px;
            background: rgba(240,192,64,0.15); color: #7a5c00;
            font-weight: 500; border: 0.5px solid rgba(240,192,64,0.3);
        }
        [data-theme="dark"] .level-pill { color: var(--nav-accent); }

        .theme-toggle {
            width: 34px; height: 34px; border-radius: 8px;
            border: 0.5px solid var(--color-border);
            background: none; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            color: var(--color-muted); font-size: 15px;
            transition: background 0.15s; font-family: inherit;
        }
        .theme-toggle:hover { background: var(--color-bg); color: var(--color-text); }

        /* User dropdown */
        .user-menu { position: relative; }
        .user-btn {
            display: flex; align-items: center; gap: 7px;
            padding: 4px 10px 4px 4px; border-radius: 20px;
            border: 0.5px solid var(--color-border);
            background: none; cursor: pointer; font-family: inherit;
            transition: background 0.15s, border-color 0.15s;
        }
        .user-btn:hover { background: var(--color-bg); border-color: var(--color-border); }
        .user-avatar {
            width: 28px; height: 28px; border-radius: 50%;
            background: #4f46e5; color: white;
            font-size: 11px; font-weight: 500;
            display: flex; align-items: center; justify-content: center;
        }
        .user-name { font-size: 13px; color: var(--color-text); font-weight: 500; }
        .chevron { font-size: 9px; color: var(--color-muted); transition: transform 0.2s; line-height: 1; }
        .user-btn.open .chevron { transform: rotate(180deg); }

        .dropdown {
            position: absolute; top: calc(100% + 6px); right: 0;
            background: var(--color-surface);
            border: 0.5px solid var(--color-border);
            border-radius: 12px; padding: 6px; min-width: 190px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            opacity: 0; transform: translateY(-4px);
            pointer-events: none;
            transition: opacity 0.15s, transform 0.15s;
            z-index: 200;
        }
        [data-theme="dark"] .dropdown { box-shadow: 0 8px 24px rgba(0,0,0,0.4); }
        .dropdown.visible { opacity: 1; transform: translateY(0); pointer-events: auto; }

        .drop-header {
            padding: 8px 10px 10px;
            border-bottom: 0.5px solid var(--color-border);
            margin-bottom: 4px;
        }
        .drop-name  { font-size: 13px; font-weight: 500; color: var(--color-text); }
        .drop-email { font-size: 11.5px; color: var(--color-muted); margin-top: 2px; }
        .drop-item {
            display: flex; align-items: center; gap: 8px;
            padding: 7px 10px; border-radius: 8px;
            font-size: 13px; color: var(--color-text);
            text-decoration: none; background: none; border: none;
            width: 100%; cursor: pointer; font-family: inherit;
            transition: background 0.12s; text-align: left;
        }
        .drop-item:hover { background: var(--color-bg); }
        .drop-item svg { width: 14px; height: 14px; flex-shrink: 0; color: var(--color-muted); }
        .drop-item.danger { color: #a32d2d; }
        .drop-item.danger svg { color: #a32d2d; }
        .drop-item.danger:hover { background: rgba(163,45,45,0.07); }
        [data-theme="dark"] .drop-item.danger:hover { background: rgba(163,45,45,0.15); }
        .drop-sep { height: 0.5px; background: var(--color-border); margin: 4px 0; }

        /* Auth links */
        .nav-auth-link {
            padding: 6px 14px; border-radius: 8px; font-size: 13.5px;
            text-decoration: none; font-weight: 500;
            transition: background 0.15s;
        }
        .nav-auth-link.ghost { color: var(--color-muted); }
        .nav-auth-link.ghost:hover { background: var(--color-bg); color: var(--color-text); }
        .nav-auth-link.filled {
            background: #4f46e5; color: white; border-radius: 8px;
        }
        .nav-auth-link.filled:hover { background: #4338ca; }

        /* ── Main ── */
        main {
            flex: 1; padding: 32px 24px;
            max-width: 1100px; width: 100%; margin: 0 auto;
        }

        .alert-success {
            background: #f0fff4; border: 0.5px solid #9fe1cb;
            color: #085041; border-radius: 10px;
            padding: 12px 16px; margin-bottom: 20px; font-size: 14px;
        }
        [data-theme="dark"] .alert-success {
            background: rgba(8,80,65,0.2);
            border-color: rgba(159,225,203,0.2);
            color: #9fe1cb;
        }

        /* ── Footer ── */
        footer {
            background: var(--color-surface);
            border-top: 0.5px solid var(--color-border);
            padding: 20px 24px;
        }
        .foot-inner {
            max-width: 1100px; margin: 0 auto;
            display: flex; align-items: center;
            justify-content: space-between; gap: 16px;
        }
        .foot-brand {
            display: flex; align-items: center; gap: 8px;
            font-size: 14px; font-weight: 500; color: var(--color-text);
        }
        .foot-brand-icon {
            width: 22px; height: 22px; background: var(--nav-accent);
            border-radius: 6px; display: flex; align-items: center;
            justify-content: center; font-size: 12px;
        }
        .foot-links { display: flex; gap: 16px; }
        .foot-link { font-size: 13px; color: var(--color-muted); text-decoration: none; }
        .foot-link:hover { color: var(--color-text); }
        .foot-copy { font-size: 12px; color: var(--color-muted); }

        /* ── Mobile ── */
        @media (max-width: 640px) {
            .level-pill { display: none; }
            .nav-link span.label { display: none; }
            .user-name { display: none; }
        }
    </style>
    @stack('styles')
</head>
<body>

<nav>
    <a href="/dashboard" class="nav-brand">
        <div class="nav-brand-icon">♟</div>
        Гарри
    </a>

    @auth
    <div class="nav-links">
        <a href="/dashboard"  class="nav-link @if(request()->is('dashboard'))  active @endif">
            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="2" width="5" height="5" rx="1"/><rect x="9" y="2" width="5" height="5" rx="1"/><rect x="2" y="9" width="5" height="5" rx="1"/><rect x="9" y="9" width="5" height="5" rx="1"/></svg>
            Дашборд
        </a>
        <a href="/trainer"    class="nav-link @if(request()->is('trainer*'))    active @endif">
            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="8" cy="5" r="2.5"/><path d="M8 9c-3 0-5 1.3-5 3v1h10v-1c0-1.7-2-3-5-3z"/></svg>
            Тренер
        </a>
        <a href="/diagnostic" class="nav-link @if(request()->is('diagnostic*')) active @endif">
            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="8" cy="8" r="5.5"/><path d="M8 5v3.5l2 1.5"/></svg>
            Диагностика
        </a>
        <a href="/progress"   class="nav-link @if(request()->is('progress'))    active @endif">
            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="2,12 5,7 8,9 11,4 14,6"/></svg>
            Прогресс
        </a>
    </div>

    <div class="nav-right">
        <div class="level-pill">♟ Уровень {{ auth()->user()->level }}/5</div>

        <button class="theme-toggle" id="themeBtn" title="Переключить тему">
            <span id="themeIcon">☀</span>
        </button>

        <div class="user-menu">
            <button class="user-btn" id="userBtn">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                <span class="user-name">{{ explode(' ', auth()->user()->name)[0] }}</span>
                <span class="chevron">▾</span>
            </button>
            <div class="dropdown" id="navDropdown">
                <div class="drop-header">
                    <div class="drop-name">{{ auth()->user()->name }}</div>
                    <div class="drop-email">{{ auth()->user()->email }}</div>
                </div>
                <a href="/profile" class="drop-item">
                    <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="8" cy="5" r="2.5"/><path d="M8 9c-3 0-5 1.3-5 3v1h10v-1c0-1.7-2-3-5-3z"/></svg>
                    Профиль и настройки
                </a>
                <div class="drop-sep"></div>
                <form method="POST" action="/logout" style="margin:0">
                    @csrf
                    <button type="submit" class="drop-item danger">
                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 3H3v10h3M10 11l3-3-3-3M6 8h7"/></svg>
                        Выйти
                    </button>
                </form>
            </div>
        </div>
    </div>

    @else
    <div class="nav-right">
        <button class="theme-toggle" id="themeBtn" title="Переключить тему">
            <span id="themeIcon">☀</span>
        </button>
        <a href="/login"    class="nav-auth-link ghost">Войти</a>
        <a href="/register" class="nav-auth-link filled">Регистрация</a>
    </div>
    @endauth
</nav>

<main>
    @if(session('level'))
        <div class="alert-success">
            Диагностика пройдена! Твой уровень: {{ session('level') }}/5
        </div>
    @endif
    @yield('content')
</main>

<footer>
    <div class="foot-inner">
        <div class="foot-brand">
            <div class="foot-brand-icon">♟</div>
            Гарри — ИИ-тренер по шахматам
        </div>
        <div class="foot-links">
            <a href="#" class="foot-link">О проекте</a>
            <a href="#" class="foot-link">Помощь</a>
        </div>
        <span class="foot-copy">© 2026 Гарри AI</span>
    </div>
</footer>

<script>
(function () {
    const saved = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', saved);
    const icon = document.getElementById('themeIcon');
    if (icon) icon.textContent = saved === 'dark' ? '☽' : '☀';
})();

document.getElementById('themeBtn')?.addEventListener('click', () => {
    const html = document.documentElement;
    const next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
    document.getElementById('themeIcon').textContent = next === 'dark' ? '☽' : '☀';
});

const userBtn = document.getElementById('userBtn');
const navDropdown = document.getElementById('navDropdown');

userBtn?.addEventListener('click', (e) => {
    e.stopPropagation();
    const open = navDropdown.classList.toggle('visible');
    userBtn.classList.toggle('open', open);
});

document.addEventListener('click', () => {
    navDropdown?.classList.remove('visible');
    userBtn?.classList.remove('open');
});
</script>

@stack('scripts')
</body>
</html>