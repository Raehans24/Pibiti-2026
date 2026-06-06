<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\QuizController;
use Illuminate\Http\Request;
use Illuminate\Mail\Markdown;
use Illuminate\Support\Facades\Route;

use function Laravel\Ai\agent;

Route::get('/', function () {
    return view('dashboard');
});

Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showlogin')->name('login');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout');
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
});

// Route::get('/playground-ai',function(Request $request){
//     $prompt = $request->input('prompt');

//     if(empty($prompt)){
//         return view('playground-ai');
//     }

//     $response = agent(
//         instructions: 'Kamu adalah seorang mentor laravel yang membantu saya belajar pemrograman
//         Berikan jawaban yang singkat, jelas, dan mudah dipahami'
//     )->prompt($prompt);

//     // // $answer = Markdown::parse($response);

//     // // return $answer;
//     // dd ($response);
//     if(isset($prompt)){
//         return view('playground-ai');
//     }
// });
