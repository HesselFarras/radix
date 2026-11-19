<?php

namespace App\Http\Controllers;

use App\Models\Understanding;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UnderstandingController extends Controller
{
    public function show(Understanding $understanding): View
    {
        $understanding->load(['topic.module', 'questions.options']);

        return view('understandings.show', compact('understanding'));
    }

    public function submit(Request $request, Understanding $understanding): View
    {
        $understanding->load('questions.options');
        $answers = $request->validate(['answers' => ['required', 'array']])['answers'];
        $total = $understanding->questions->sum('points');
        $earned = 0;

        foreach ($understanding->questions as $question) {
            $selected = $answers[$question->id] ?? null;
            if ($question->options->contains(fn ($option) => $option->is_correct && (string) $option->id === (string) $selected)) {
                $earned += $question->points;
            }
        }

        $score = $total ? (int) round($earned / $total * 100) : 0;
        $passed = $score >= $understanding->passing_score;
        $attempt = $understanding->attempts()->create([
            'user_id' => auth()->id(),
            'score' => $score,
            'passed' => $passed,
            'answers_json' => $answers,
            'completed_at' => now(),
        ]);

        return view('understandings.result', compact('understanding', 'attempt'));
    }
}
