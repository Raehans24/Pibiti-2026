<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $totalNotes = $user->notes()->count();
        $totalQuizzes = $user->quizzes()->count();
        $totalScores = $user->quizScores()->count();

        return view('dashboard', compact('totalNotes', 'totalQuizzes', 'totalScores'));
    }
}
