<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\WorldController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'login');
    Route::get('/register', 'showRegister')->name('register');
    Route::post('/register', 'register');
    Route::post('/logout', 'logout')->name('logout');

    // Google OAuth
    Route::get('/auth/google', 'redirectToGoogle')->name('auth.google');
    Route::get('/auth/google/callback', 'handleGoogleCallback')->name('auth.google.callback');
});

Route::middleware('auth')->group(function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard');
    });

    Route::controller(NotesController::class)->group(function () {
        Route::get('/notes', 'index')->name('notes');
        Route::post('/notes', 'store')->name('notes.store');
        Route::post('/notes/upload', 'upload')->name('notes.upload');
        Route::get('/notes/{id}', 'show')->name('notes.show');
        Route::get('/notes/{id}/file', 'file')->name('notes.file');
        Route::post('/notes/{id}/summary', 'summary')->name('notes.summary');
        Route::put('/notes/{id}', 'update')->name('notes.update');
        Route::delete('/notes/{id}', 'destroy')->name('notes.destroy');
    });

    Route::controller(QuizController::class)->group(function () {
        Route::get('/quiz', 'index')->name('quiz');
        Route::get('/quiz/{id}', 'show')->name('quiz.show');
        Route::post('/quiz/{id}/submit', 'submit')->name('quiz.submit');
        Route::delete('/quiz/{id}', 'destroy')->name('quiz.destroy');
        Route::get('/notes/{id}/quiz', 'generate')->name('quiz.generate');
    });

    // World Prediction Globe
    Route::controller(WorldController::class)->group(function () {
        Route::get('/world', 'index')->name('world');
        Route::get('/api/world/weather', 'weather')->name('world.weather');
        Route::get('/api/world/wind', 'wind')->name('world.wind');
        Route::get('/api/world/commodities', 'commodities')->name('world.commodities');
        Route::get('/api/world/events', 'events')->name('world.events');
    });
});
