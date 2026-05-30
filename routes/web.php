<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
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
    Route::get('/notes','notes')->name('notes');
})->middleware('auth');