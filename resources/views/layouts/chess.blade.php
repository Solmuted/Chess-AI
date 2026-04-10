<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ChessMind') }} — @yield('title')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Crimson+Pro:ital,wght@0,300;0,400;1,300&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --ivory:    #F0E6C8;
            --ebony:    #1A1209;
            --dark:     #110D06;
            --gold:     #C8A84B;
            --gold-dim: #7A6228;
            --cream:    #D4C49A;
            --error:    #C84B4B;
            --radius:   4px;
        }

        html, body { height: 100%; }

        /* ── Background with animated grid ── */
        body {
            background-color: var(--dark);
            font-family: 'Crimson Pro', Georgia, serif;
            color: var(--ivory);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* Animated grid lines */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                repeating-linear-gradient(0deg,   transparent, transparent 59px, rgba(200,168,75,0.05) 59px, rgba(200,168,75,0.05) 60px),
                repeating-linear-gradient(90deg,  transparent, transparent 59px, rgba(200,168,75,0.05) 59px, rgba(200,168,75,0.05) 60px);
            animation: gridDrift 20s linear infinite;
            pointer-events: none;
            z-index: 0;
        }

        @keyframes gridDrift {
            0%   { background-position: 0 0, 0 0; }
            100% { background-position: 60px 60px, 60px 60px; }
        }

        /* ── Floating chess pieces background ── */
        .chess-bg {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }

        .chess-piece {
            position: absolute;
            font-size: 2.2rem;
            opacity: 0;
            color: rgba(200,168,75,0.07);
            animation: floatPiece var(--dur, 18s) var(--delay, 0s) ease-in-out infinite;
            user-select: none;
        }

        @keyframes floatPiece {
            0%   { opacity: 0;    transform: translateY(110vh) rotate(var(--rot0, -10deg)); }
            10%  { opacity: 1; }
            90%  { opacity: 1; }
            100% { opacity: 0;    transform: translateY(-15vh)  rotate(var(--rot1,  10deg)); }
        }

        /* ── Ambient glow orbs ── */
        .orb {
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
            filter: blur(80px);
            animation: orbFloat var(--dur, 12s) ease-in-out infinite alternate;
        }

        .orb-1 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(200,168,75,0.06) 0%, transparent 70%);
            top: -120px; left: -120px;
            --dur: 14s;
        }

        .orb-2 {
            width: 350px; height: 350px;
            background: radial-gradient(circle, rgba(200,100,30,0.04) 0%, transparent 70%);
            bottom: -100px; right: -100px;
            --dur: 18s;
        }

        @keyframes orbFloat {
            from { transform: translate(0, 0) scale(1); }
            to   { transform: translate(30px, 20px) scale(1.12); }
        }

        /* ── Page wrapper ── */
        .auth-scene {
            position: relative;
            z-index: 1;
            width: 100%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        /* ── Card ── */
        .auth-card {
            position: relative;
            width: 100%;
            max-width: 420px;
            background: linear-gradient(160deg, #221809 0%, #16100A 100%);
            border: 1px solid rgba(200,168,75,0.22);
            border-radius: 2px;
            padding: 3rem 2.8rem 2.5rem;
            box-shadow:
                0 0 0 1px rgba(0,0,0,0.7),
                0 32px 90px rgba(0,0,0,0.7),
                inset 0 1px 0 rgba(200,168,75,0.15);
            animation: cardIn 0.7s cubic-bezier(0.22,1,0.36,1) both;
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(32px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0)    scale(1); }
        }

        /* Animated top gold bar */
        .auth-card::before {
            content: '';
            position: absolute;
            top: 0; left: 50%; right: 50%;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
            border-radius: 0 0 2px 2px;
            animation: barExpand 0.9s 0.3s cubic-bezier(0.22,1,0.36,1) forwards;
        }

        @keyframes barExpand {
            to { left: 10%; right: 10%; }
        }

        /* Shimmer sweep on card border */
        .auth-card::after {
            content: '';
            position: absolute;
            inset: -1px;
            border-radius: 2px;
            background: linear-gradient(
                105deg,
                transparent 30%,
                rgba(200,168,75,0.15) 50%,
                transparent 70%
            );
            background-size: 200% 100%;
            animation: borderShimmer 4s 1s linear infinite;
            pointer-events: none;
        }

        @keyframes borderShimmer {
            0%   { background-position: -100% 0; }
            100% { background-position: 300% 0; }
        }

        /* ── Brand / Logo ── */
        .brand {
            text-align: center;
            margin-bottom: 2.4rem;
            animation: fadeUp 0.6s 0.15s both;
        }

        .brand-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            border: 1px solid var(--gold-dim);
            border-radius: 2px;
            margin-bottom: 1rem;
            position: relative;
            overflow: hidden;
            animation: iconPulse 3s 1s ease-in-out infinite;
        }

        @keyframes iconPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(200,168,75,0); }
            50%       { box-shadow: 0 0 16px 4px rgba(200,168,75,0.15); }
        }

        .brand-icon::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(200,168,75,0.12), transparent);
        }

        /* Icon shimmer sweep */
        .brand-icon::after {
            content: '';
            position: absolute;
            top: -50%; left: -60%;
            width: 40%; height: 200%;
            background: linear-gradient(105deg, transparent, rgba(200,168,75,0.25), transparent);
            transform: skewX(-15deg);
            animation: iconShimmer 4s 1.5s ease-in-out infinite;
        }

        @keyframes iconShimmer {
            0%, 100% { left: -60%; }
            30%       { left: 140%; }
        }

        .brand-icon svg {
            width: 28px;
            height: 28px;
            fill: var(--gold);
            position: relative;
            z-index: 1;
        }

        .brand-name {
            font-family: 'Cinzel', serif;
            font-size: 1.35rem;
            font-weight: 700;
            letter-spacing: 0.18em;
            color: var(--ivory);
            text-transform: uppercase;
        }

        .brand-name span { color: var(--gold); }

        .brand-tagline {
            font-size: 0.78rem;
            color: var(--gold-dim);
            letter-spacing: 0.22em;
            text-transform: uppercase;
            margin-top: 0.3rem;
        }

        /* ── Page title ── */
        .auth-title {
            font-family: 'Cinzel', serif;
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.28em;
            text-transform: uppercase;
            color: var(--gold);
            text-align: center;
            margin-bottom: 2rem;
            animation: fadeUp 0.6s 0.25s both;
        }

        .auth-title::before,
        .auth-title::after {
            content: '⬥';
            color: var(--gold-dim);
            margin: 0 0.6rem;
            font-size: 0.55rem;
            vertical-align: middle;
            animation: diamondSpin 6s linear infinite;
            display: inline-block;
        }

        @keyframes diamondSpin {
            0%, 40%, 100% { transform: rotate(0deg); }
            50%             { transform: rotate(180deg); }
        }

        /* ── Form fields — stagger animation ── */
        .form-group {
            margin-bottom: 1.3rem;
            animation: fadeUp 0.5s both;
        }

        /* Each group delays via nth-child */
        .form-group:nth-child(1) { animation-delay: 0.35s; }
        .form-group:nth-child(2) { animation-delay: 0.45s; }
        .form-group:nth-child(3) { animation-delay: 0.55s; }
        .form-group:nth-child(4) { animation-delay: 0.65s; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .form-label {
            display: block;
            font-size: 0.72rem;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--cream);
            margin-bottom: 0.5rem;
            opacity: 0.7;
            transition: opacity 0.2s, color 0.2s;
        }

        /* Label lifts on focus */
        .form-group:focus-within .form-label {
            opacity: 1;
            color: var(--gold);
        }

        .form-input {
            width: 100%;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(200,168,75,0.18);
            border-radius: var(--radius);
            padding: 0.75rem 1rem;
            font-family: 'Crimson Pro', Georgia, serif;
            font-size: 1rem;
            color: var(--ivory);
            outline: none;
            transition: border-color 0.25s, box-shadow 0.25s, background 0.25s, transform 0.15s;
        }

        .form-input::placeholder { color: rgba(240,230,200,0.2); }

        .form-input:focus {
            border-color: var(--gold);
            background: rgba(200,168,75,0.05);
            box-shadow: 0 0 0 3px rgba(200,168,75,0.08), 0 2px 12px rgba(200,168,75,0.06);
            transform: translateY(-1px);
        }

        .form-input.is-invalid {
            border-color: var(--error);
            animation: shake 0.4s cubic-bezier(0.36, 0.07, 0.19, 0.97);
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%       { transform: translateX(-6px); }
            40%       { transform: translateX(6px); }
            60%       { transform: translateX(-4px); }
            80%       { transform: translateX(4px); }
        }

        .invalid-feedback {
            font-size: 0.8rem;
            color: var(--error);
            margin-top: 0.35rem;
            opacity: 0;
            animation: fadeIn 0.3s 0.1s forwards;
        }

        @keyframes fadeIn {
            to { opacity: 0.9; }
        }

        /* ── Checkbox row ── */
        .form-check {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 1.3rem;
            animation: fadeUp 0.5s 0.65s both;
        }

        .form-check input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--gold);
            cursor: pointer;
        }

        .form-check label {
            font-size: 0.85rem;
            color: var(--cream);
            opacity: 0.75;
            cursor: pointer;
        }

        /* ── Primary button with shimmer + ripple ── */
        .btn-primary {
            width: 100%;
            padding: 0.85rem;
            background: linear-gradient(135deg, #C8A84B 0%, #A88A35 100%);
            border: none;
            border-radius: var(--radius);
            font-family: 'Cinzel', serif;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.28em;
            text-transform: uppercase;
            color: var(--ebony);
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: opacity 0.2s, transform 0.15s, box-shadow 0.2s;
            margin-top: 0.5rem;
            animation: fadeUp 0.5s 0.75s both;
        }

        /* Shimmer on button */
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0; left: -75%;
            width: 50%; height: 100%;
            background: linear-gradient(105deg, transparent, rgba(255,255,255,0.35), transparent);
            transform: skewX(-20deg);
            animation: btnShimmer 3s 2s ease-in-out infinite;
        }

        @keyframes btnShimmer {
            0%, 100% { left: -75%; }
            40%       { left: 125%; }
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(200,168,75,0.3);
        }

        .btn-primary:active {
            transform: translateY(0);
            box-shadow: none;
        }

        /* Ripple element (injected via JS) */
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
            transform: scale(0);
            animation: rippleAnim 0.55s linear;
            pointer-events: none;
        }

        @keyframes rippleAnim {
            to { transform: scale(4); opacity: 0; }
        }

        /* ── Ghost / link button ── */
        .btn-ghost {
            background: none;
            border: none;
            color: var(--gold);
            font-family: 'Crimson Pro', serif;
            font-size: 0.9rem;
            cursor: pointer;
            text-decoration: none;
            transition: color 0.2s, letter-spacing 0.2s;
        }

        .btn-ghost:hover {
            color: var(--ivory);
            letter-spacing: 0.04em;
        }

        /* ── Footer ── */
        .auth-footer {
            text-align: center;
            margin-top: 1.6rem;
            font-size: 0.88rem;
            color: var(--cream);
            opacity: 0;
            animation: fadeIn 0.5s 0.9s forwards;
        }

        .auth-footer a {
            color: var(--gold);
            text-decoration: none;
            transition: color 0.2s;
            position: relative;
        }

        /* Underline slide animation */
        .auth-footer a::after {
            content: '';
            position: absolute;
            bottom: -1px; left: 0; right: 100%;
            height: 1px;
            background: var(--gold);
            transition: right 0.25s ease;
        }

        .auth-footer a:hover::after { right: 0; }
        .auth-footer a:hover { color: var(--ivory); }

        /* ── Status messages ── */
        .auth-status {
            padding: 0.75rem 1rem;
            border-radius: var(--radius);
            font-size: 0.88rem;
            margin-bottom: 1.4rem;
            border-left: 3px solid;
            animation: slideIn 0.4s cubic-bezier(0.22,1,0.36,1);
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-10px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        .auth-status.success {
            background: rgba(75,180,75,0.08);
            border-color: #4BB44B;
            color: #8DD88D;
        }

        .auth-status.error {
            background: rgba(200,75,75,0.08);
            border-color: var(--error);
            color: #E09090;
        }

        /* ── Hint text (forgot password page) ── */
        .auth-hint {
            text-align: center;
            font-size: 0.92rem;
            color: var(--cream);
            opacity: 0.6;
            margin-bottom: 1.8rem;
            line-height: 1.65;
            animation: fadeUp 0.5s 0.3s both;
        }

        /* ── Misc row (remember + forgot) ── */
        .form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
            animation: fadeUp 0.5s 0.65s both;
        }

        .form-row .form-check { margin-bottom: 0; animation: none; }
    </style>

    @stack('styles')
</head>
<body>

{{-- Ambient orbs --}}
<div class="orb orb-1"></div>
<div class="orb orb-2"></div>

{{-- Floating chess pieces --}}
<div class="chess-bg" aria-hidden="true">
    <span class="chess-piece" style="left:8%;  --dur:22s; --delay:0s;   --rot0:-8deg;  --rot1:8deg;">♔</span>
    <span class="chess-piece" style="left:18%; --dur:17s; --delay:3s;   --rot0:5deg;   --rot1:-5deg;">♛</span>
    <span class="chess-piece" style="left:32%; --dur:25s; --delay:7s;   --rot0:-12deg; --rot1:6deg;">♞</span>
    <span class="chess-piece" style="left:48%; --dur:19s; --delay:1s;   --rot0:8deg;   --rot1:-10deg;">♝</span>
    <span class="chess-piece" style="left:62%; --dur:23s; --delay:5s;   --rot0:-6deg;  --rot1:12deg;">♜</span>
    <span class="chess-piece" style="left:75%; --dur:16s; --delay:9s;   --rot0:10deg;  --rot1:-8deg;">♟</span>
    <span class="chess-piece" style="left:88%; --dur:20s; --delay:2s;   --rot0:-15deg; --rot1:5deg;">♚</span>
    <span class="chess-piece" style="left:25%; --dur:28s; --delay:12s;  --rot0:4deg;   --rot1:-14deg;">♕</span>
    <span class="chess-piece" style="left:55%; --dur:21s; --delay:15s;  --rot0:-9deg;  --rot1:9deg;">♗</span>
    <span class="chess-piece" style="left:72%; --dur:18s; --delay:8s;   --rot0:7deg;   --rot1:-7deg;">♘</span>
</div>

<div class="auth-scene">
    @yield('content')
</div>

<script>
    // Ripple effect on all .btn-primary buttons
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-primary');
        if (!btn) return;
        const r = document.createElement('span');
        r.classList.add('ripple');
        const rect = btn.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        r.style.cssText = `width:${size}px;height:${size}px;left:${e.clientX-rect.left-size/2}px;top:${e.clientY-rect.top-size/2}px`;
        btn.appendChild(r);
        r.addEventListener('animationend', () => r.remove());
    });
</script>

</body>
</html>
