<?php

namespace App\Http\Controllers;

use App\Ai\Agents\QuizAgent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $notes = $user->notes()->whereHas('quizzes')->with('quizzes')->get();

        return view('quiz.index', compact('notes'));
    }

    public function show($id)
    {
        /** @var User $user */
        $user = Auth::user();
        $note = $user->notes()->findOrFail($id);

        $quiz = $note->quizzes()->latest()->first();

        return view('quiz.show', [
            'note' => $note,
            'quiz' => $quiz,
        ]);
    }

    public function generate($id)
    {
        /** @var User $user */
        $user = Auth::user();
        $note = $user->notes()->findOrFail($id);

        $quizData = QuizAgent::make()->prompt(
            $note->summary ?: $note->content
        );

        $data = is_string($quizData) ? json_decode($quizData, true) : 
                ($quizData instanceof \Illuminate\Contracts\Support\Arrayable ? $quizData->toArray() : (array) $quizData);

        $user->quizzes()->create([
            'note_id' => $note->id,
            'data' => $data,
        ]);

        return redirect('/quiz/'.$id);
    }

    public function submit(Request $request, $id)
    {
        /** @var User $user */
        $user = Auth::user();
        $note = $user->notes()->findOrFail($id);

        $quiz = $note->quizzes()->latest()->first();
        if (! $quiz) {
            abort(404, 'Quiz not found');
        }

        $quizData = $quiz->data;
        $questions = is_array($quizData) ? ($quizData['question'] ?? []) : ($quizData->question ?? []);
        $userAnswers = $request->input('answers', []);

        $correctCount = 0;
        $totalQuestions = count($questions);
        $results = [];

        foreach ($questions as $index => $q) {
            $qArray = is_array($q) ? $q : (array) $q;
            $userAns = $userAnswers[$index] ?? null;
            $correctAns = $qArray['answer'] ?? null;
            $isCorrect = $userAns === $correctAns;

            if ($isCorrect) {
                $correctCount++;
            }

            $results[$index] = [
                'user_answer' => $userAns,
                'is_correct' => $isCorrect,
                'correct_answer' => $correctAns,
            ];
        }

        $score = $totalQuestions > 0 ? (int) round(($correctCount / $totalQuestions) * 100) : 0;

        $user->quizScores()->create([
            'quiz_id' => $quiz->id,
            'score' => $score,
            'correct_count' => $correctCount,
            'total_questions' => $totalQuestions,
            'details' => $results,
        ]);

        return redirect()->route('quiz.show', $id)->with('quiz_result', [
            'score' => $score,
            'correct_count' => $correctCount,
            'total_questions' => $totalQuestions,
            'details' => $results,
        ]);
    }

    public function destroy($id)
    {
        /** @var User $user */
        $user = Auth::user();
        $note = $user->notes()->findOrFail($id);

        $note->quizzes()->delete();

        return redirect()->route('quiz');
    }
}
