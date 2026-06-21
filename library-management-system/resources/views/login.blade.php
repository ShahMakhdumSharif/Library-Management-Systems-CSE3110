@extends('layouts.public')

@section('title', 'Login | Central Library')

@section('content')
    <section class="auth-wrap" aria-labelledby="login-title">
        <div class="auth-copy">
            <div class="eyebrow">Secure access</div>
            <h1 id="login-title">Login to your library account</h1>
        </div>

        <form class="login-card" action="{{ url('/login') }}" method="post">
            @csrf
            <h2>Account Login</h2>
            @if (session('status'))
                <div class="status-message">{{ session('status') }}</div>
            @endif
            <!-- <div class="field">
                <label for="role">Login as</label>
                <select id="role" name="role">
                    <option>Admin</option>
                    <option>Librarian</option>
                    <option>Member</option>
                </select>
            </div> -->
            <div class="field">
                <label for="email">Email or Member ID</label>
                <input id="email" name="email" type="text" placeholder="Enter email or ID">
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" placeholder="Enter password">
            </div>
            <button type="submit">Login</button>
        </form>
    </section>
@endsection
