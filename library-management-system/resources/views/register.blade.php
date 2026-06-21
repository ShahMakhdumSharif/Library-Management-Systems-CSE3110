@extends('layouts.public')

@section('title', 'Register | Central Library')

@section('content')
    <section class="auth-wrap" aria-labelledby="register-title">
        <div class="auth-copy">
            <div class="eyebrow">Create account</div>
            <h1 id="register-title">Register for library access</h1>
        </div>

        <form class="login-card" action="{{ url('/register') }}" method="post">
            @csrf
            <h2>Member Registration</h2>
            @if (session('status'))
                <div class="status-message">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="status-message">
                    {{ $errors->first() }}
                </div>
            @endif
            <div class="field">
                <label for="name">Full Name</label>
                <input id="name" name="name" type="text" placeholder="Enter full name" value="{{ old('name') }}" required>
            </div>
            <div class="field">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" placeholder="Enter email address" value="{{ old('email') }}" required>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" placeholder="Create password" required>
            </div>
            <div class="field">
                <label for="password_confirmation">Confirm Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Confirm password" required>
            </div>
            <button type="submit">Register</button>
        </form>
    </section>
@endsection
