@extends('layouts.public')
@section('title', 'Log in | Central Library')
@section('content')
<div class="container auth">
    <section><div class="eyebrow">Your library account</div><h1>Welcome back.</h1><p class="lede">Sign in to review loans, reservations, fines, and library notices.</p></section>
    <form class="form-card" method="post" action="{{ route('login') }}">@csrf
        <h2>Account login</h2>
        <div class="field"><label for="email">Email address</label><input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>@error('email')<div class="error-text">{{ $message }}</div>@enderror</div>
        <div class="field" style="margin-top:18px"><label for="password">Password</label><input id="password" name="password" type="password" autocomplete="current-password" required>@error('password')<div class="error-text">{{ $message }}</div>@enderror</div>
        <label style="display:block;margin:16px 0"><input name="remember" type="checkbox" value="1"> Keep me signed in</label>
        <button class="btn" type="submit">Log in</button>
        <p class="muted">New here? <a style="color:var(--crimson);font-weight:800" href="{{ route('register') }}">Create a member account</a>.</p>
    </form>
</div>
@endsection
