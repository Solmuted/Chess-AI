@extends('layouts.chess')
@section('title', 'Sign In')
 
@section('content')
<div class="auth-card">
 
    {{-- Brand --}}
    <div class="brand">
        <div class="brand-icon">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2v2M10 4h4M11 6V4M13 6V4M9 6h6l1 2H8L9 6zM7 16c0-2.76 2.24-5 5-5s5 2.24 5 5v2H7v-2zM5 20h14v2H5v-2z"/>
            </svg>
        </div>
        <div class="brand-name">Chess<span>Mind</span></div>
        <div class="brand-tagline">AI Training Platform</div>
    </div>
 
    <div class="auth-title">Welcome Back</div>
 
    {{-- Session Status --}}
    @if (session('status'))
        <div class="auth-status success">{{ session('status') }}</div>
    @endif
 
    <form method="POST" action="{{ route('login') }}">
        @csrf
 
        {{-- Email --}}
        <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input
                id="email"
                type="email"
                name="email"
                class="form-input @error('email') is-invalid @enderror"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                placeholder="you@example.com"
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
 
        {{-- Password --}}
        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input
                id="password"
                type="password"
                name="password"
                class="form-input @error('password') is-invalid @enderror"
                required
                autocomplete="current-password"
                placeholder="Your password"
            >
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
 
        {{-- Remember Me + Forgot --}}
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
            <div class="form-check" style="margin:0;">
                <input type="checkbox" id="remember_me" name="remember">
                <label for="remember_me">Remember me</label>
            </div>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="btn-ghost" style="font-size:0.82rem;">
                    Forgot password?
                </a>
            @endif
        </div>
 
        <button type="submit" class="btn-primary">
            Enter the Board
        </button>
    </form>
 
    @if (Route::has('register'))
    <div class="auth-footer">
        No account yet?
        <a href="{{ route('register') }}">Create one</a>
    </div>
    @endif
 
</div>
@endsection