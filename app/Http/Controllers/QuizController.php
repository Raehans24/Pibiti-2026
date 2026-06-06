<?php

namespace App\Http\Controllers;
use App\Ai\Agents\QuizAgent;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index(){
        $notes = collect(session('notes', []))->filter(function($note) {
            return !empty($note['quizzes']);
        });
        return view('quiz.index', compact('notes'));
    }
    public function show($id){
        $notes = session('notes',[]);
        $note = collect($notes)->firstWhere('id',(int)$id);
        if(!$note){
            abort(404,'Notes not found');
        }
        $quiz = collect($note['quizzes'])->last();

        return view('quiz.show',[
            'notes'=>$notes,
            'quiz'=>$quiz
            
        ]);
    }
    public function generate($id){
        $notes = session('notes',[]);
        $note = collect($notes)->firstWhere('id',(int)$id);
        if(!$note){
            abort(404,'Notes not found');
        }
        $quiz = QuizAgent::make()->prompt(
            $note['summary']?:$note['content']
        );

        $notes = collect($notes)->map(function($item)use($id,$quiz){
            if ($item['id'] === (int) $id) {
                $item['quizzes'][] = $quiz;
            }
            return $item;
        })
        ->all();

        session(['notes'=>$notes]);

        return redirect('/quiz/'.$id);

    }

    public function submit(Request $request, $id)
    {
        $notes = session('notes', []);
        $note = collect($notes)->firstWhere('id', (int)$id);
        
        if (!$note || empty($note['quizzes'])) {
            abort(404, 'Quiz not found');
        }
        
        $quiz = collect($note['quizzes'])->last();
        // Handle array or object structure depending on Laravel AI parsing
        $questions = is_array($quiz) ? ($quiz['question'] ?? []) : ($quiz->question ?? []);
        $userAnswers = $request->input('answers', []);
        
        $correctCount = 0;
        $totalQuestions = count($questions);
        $results = [];
        
        foreach ($questions as $index => $q) {
            $qArray = is_array($q) ? $q : (array)$q;
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
        
        $score = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100) : 0;
        
        return redirect()->route('quiz.show', $id)->with('quiz_result', [
            'score' => $score,
            'correct_count' => $correctCount,
            'total_questions' => $totalQuestions,
            'details' => $results,
        ]);
    }

    public function destroy($id)
    {
        $notes = session('notes', []);
        
        $notes = collect($notes)->map(function($item) use ($id) {
            if ($item['id'] === (int) $id) {
                unset($item['quizzes']);
            }
            return $item;
        })->all();
        
        session(['notes' => $notes]);
        
        return redirect()->route('quiz');
    }
}
