@extends('layouts.public')
@section('title', 'Register | Central Library')
@section('content')
<div class="container auth">
    <section><div class="eyebrow">Join the library</div><h1>Your next chapter starts here.</h1><p class="lede">Create a member account to reserve books and manage your library activity.</p></section>
    <form class="form-card" method="post" action="{{ route('register') }}">@csrf
        <h2>Member registration</h2>
        <div class="field"><label for="name">Full name</label><input id="name" name="name" value="{{ old('name') }}" required>@error('name')<div class="error-text">{{ $message }}</div>@enderror</div>
        <div class="field" style="margin-top:16px"><label for="email">Email address</label><input id="email" name="email" type="email" value="{{ old('email') }}" required>@error('email')<div class="error-text">{{ $message }}</div>@enderror</div>
        <div class="field" style="margin-top:16px"><label for="password">Password</label><input id="password" name="password" type="password" minlength="8" required>@error('password')<div class="error-text">{{ $message }}</div>@enderror</div>
        <div class="field" style="margin:16px 0"><label for="password_confirmation">Confirm password</label><input id="password_confirmation" name="password_confirmation" type="password" required></div>
        <button class="btn" type="submit">Create account</button>
    </form>
</div>
@endsection
