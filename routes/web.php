<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\QuizController;
use App\Http\Middleware\CheckLogin;

Route::get('/', function () {
    return view('home');
});

Route::controller(AuthController::class)->group(function(){
    Route::get('/login','showlogin') ->name('login');
    Route::post('/login','login');
    Route::post('/logout','logout')->name('logout');
});

Route::controller(DashboardController::class)->group(function(){
    Route::get('/dashboard','index')->name('dashboard');
})->middleware('auth');

Route::controller(NotesController::class)->group(function(){
    Route::get('/notes','index')->name('notes');
})->middleware('auth');

Route::controller(QuizController::class)->group(function(){
    Route::get('/quiz','index')->name('quiz');
    Route::get('/show-quiz','show')->name('showQuiz');
})->middleware('auth');