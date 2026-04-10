@extends('layouts.chess')
@section('title', 'Reset Password')

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

    <div class="auth-title">Reset Password</div>

    <p style="text-align:center; font-size:0.92rem; color:var(--cream); opacity:0.65; margin-bottom:1.8rem; line-height:1.6;">
        Enter your email and we'll send a link to restore access to your account.
    </p>

    {{-- Session Status --}}
    @if (session('status'))
        <div class="auth-status success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
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
                placeholder="you@example.com"
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-primary">
            Send Reset Link
        </button>
    </form>

    <div class="auth-footer">
        Remembered it?
        <a href="{{ route('login') }}">Back to Sign In</a>
    </div>

</div>
@endsection
