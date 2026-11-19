<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\XpTransaction;
use App\Services\BadgeEvaluator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function show(Quiz $quiz): View
    {
        abort_unless($quiz->is_published, 404);
        $quiz->load(['module', 'questions.options']);

        return view('quizzes.show', compact('quiz'));
    }

    public function submit(Request $request, Quiz $quiz, BadgeEvaluator $badges): View
    {
        abort_unless($quiz->is_published, 404);
        $quiz->load('questions.options');
        $answers = $request->validate(['answers' => ['required', 'array']])['answers'];
        $total = $quiz->questions->sum('points');
        $earned = 0;

        foreach ($quiz->questions as $question) {
            $selected = $answers[$question->id] ?? null;
            if ($question->options->contains(fn ($option) => $option->is_correct && (string) $option->id === (string) $selected)) {
                $earned += $question->points;
            }
        }

        $score = $total ? (int) round($earned / $total * 100) : 0;
        $passed = $score >= $quiz->passing_score;
        $xp = $passed ? (int) round($score / 100 * $quiz->max_xp) : 0;
        $attempt = DB::transaction(function () use ($quiz, $score, $passed, $xp, $answers) {
            $attempt = $quiz->attempts()->create([
                'user_id' => auth()->id(),
                'score' => $score,
                'passed' => $passed,
                'xp_earned' => $xp,
                'answers_json' => $answers,
                'completed_at' => now(),
            ]);

            if ($xp > 0) {
                $transaction = XpTransaction::firstOrCreate(
                    ['idempotency_key' => 'quiz-attempt-'.$attempt->id],
                    [
                        'user_id' => auth()->id(),
                        'source_type' => 'quiz_attempt',
                        'source_id' => $attempt->id,
                        'amount' => $xp,
                        'description' => 'XP dari '.$quiz->title,
                    ],
                );
                if ($transaction->wasRecentlyCreated) {
                    auth()->user()->increment('total_xp', $xp);
                }
            }

            return $attempt;
        });

        $badges->evaluate(auth()->user());

        return view('quizzes.result', compact('quiz', 'attempt'));
    }
}
