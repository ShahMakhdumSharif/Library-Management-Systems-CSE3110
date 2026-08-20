<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LibraryController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LibraryController::class, 'home'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [LibraryController::class, 'dashboard'])->name('dashboard');
    Route::get('/books', [LibraryController::class, 'books'])->name('books.index');
    Route::get('/books/create', [LibraryController::class, 'createBook'])->name('books.create');
    Route::post('/books', [LibraryController::class, 'storeBook'])->name('books.store');
    Route::get('/books/{id}/edit', [LibraryController::class, 'editBook'])->name('books.edit');
    Route::put('/books/{id}', [LibraryController::class, 'updateBook'])->name('books.update');
    Route::delete('/books/{id}', [LibraryController::class, 'deleteBook'])->name('books.delete');
    Route::post('/books/{id}/reserve', [LibraryController::class, 'reserve'])->name('books.reserve');
    Route::get('/circulation', [LibraryController::class, 'circulation'])->name('circulation');
    Route::post('/circulation/issue', [LibraryController::class, 'issue'])->name('circulation.issue');
    Route::post('/circulation/{id}/return', [LibraryController::class, 'returnBook'])->name('circulation.return');
    Route::get('/reservations', [LibraryController::class, 'reservations'])->name('reservations');
    Route::post('/reservations/{id}/complete', [LibraryController::class, 'completeReservation'])->name('reservations.complete');
    Route::get('/fines', [LibraryController::class, 'fines'])->name('fines');
    Route::post('/fines/{id}/pay', [LibraryController::class, 'payFine'])->name('fines.pay');
    Route::get('/members', [LibraryController::class, 'members'])->name('members');
    Route::put('/members/{id}', [LibraryController::class, 'updateMember'])->name('members.update');
    Route::get('/notifications', [LibraryController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/{id}/read', [LibraryController::class, 'readNotification'])->name('notifications.read');
    Route::get('/reports', [LibraryController::class, 'reports'])->name('reports');
});
