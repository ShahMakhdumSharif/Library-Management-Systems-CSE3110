<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('login');
});

Route::post('/login', function () {
    return back()->with('status', 'Login auth ekhno connect kori nai.');
});

Route::get('/register', function () {
    return view('register');
});

Route::post('/register', function (Request $request) {
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    $memberRoleId = DB::table('roles')->where('role_name', 'Member')->value('id');

    if (! $memberRoleId) {
        return back()
            ->withInput($request->only('name', 'email'))
            ->with('status', 'Member role is missing. Please run php artisan db:seed.');
    }

    User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => $validated['password'],
        'role_id' => $memberRoleId,
        'status' => 'active',
    ]);

    return redirect('/login')->with('status', 'Registration successful');
});
