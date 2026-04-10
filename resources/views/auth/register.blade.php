@extends('layouts.chess')
@section('title', 'Create Account')

@section('content')
<div class="auth-card">

    {{-- Brand --}}
    <div class="brand">
        <div class="brand-icon">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <!-- Chess King SVG -->
                <path d="M12 2v2M10 4h4M11 6V4M13 6V4M9 6h6l1 2H8L9 6zM7 16c0-2.76 2.24-5 5-5s5 2.24 5 5v2H7v-2zM5 20h14v2H5v-2z"/>
            </svg>
        </div>
        <div class="brand-name">Chess<span>Mind</span></div>
        <div class="brand-tagline">AI Training Platform</div>
    </div>

    <div class="auth-title">Create Account</div>

    {{-- Session Status --}}
    @if (session('status'))
        <div class="auth-status success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Name --}}
        <div class="form-group">
            <label class="form-label" for="name">Full Name</label>
            <input
                id="name"
                type="text"
                name="name"
                class="form-input @error('name') is-invalid @enderror"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
                placeholder="Garry Kasparov"
            >
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

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
                autocomplete="new-password"
                placeholder="Min 8 characters"
            >
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div class="form-group">
            <label class="form-label" for="password_confirmation">Confirm Password</label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                class="form-input"
                required
                autocomplete="new-password"
                placeholder="Repeat password"
            >
        </div>

        <button type="submit" class="btn-primary">
            Begin Your Journey
        </button>
    </form>

    <div class="auth-footer">
        Already have an account?
        <a href="{{ route('login') }}">Sign in</a>
    </div>

</div>
@endsection