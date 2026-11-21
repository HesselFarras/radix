<?php

namespace App\Http\Controllers;

use App\Models\Understanding;
use App\Models\TopicProgress;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UnderstandingController extends Controller
{
    public function show(Understanding $understanding): View
    {
        abort_unless($understanding->topic()->where('is_published', true)->whereHas('module', fn ($query) => $query->where('is_published', true)->whereHas('course', fn ($course) => $course->where('is_published', true)))->exists(), 404);
        $understanding->load(['topic.module', 'questions.options']);

        return view('understandings.show', compact('understanding'));
    }

    public function submit(Request $request, Understanding $understanding): View
    {
        abort_unless($understanding->topic()->where('is_published', true)->whereHas('module', fn ($query) => $query->where('is_published', true)->whereHas('course', fn ($course) => $course->where('is_published', true)))->exists(), 404);
        $understanding->load('questions.options');
        $answers = $request->validate(['answers' => ['required', 'array']])['answers'];
        $questionIds = $understanding->questions->pluck('id')->map(fn ($id) => (string) $id)->all();
        abort_unless(count($answers) === count($questionIds) && ! array_diff($questionIds, array_map('strval', array_keys($answers))), 422, 'Semua soal wajib dijawab.');
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

        if ($passed) {
            TopicProgress::updateOrCreate(
                ['user_id' => auth()->id(), 'topic_id' => $understanding->topic_id],
                ['completed_at' => now(), 'last_read_at' => now()],
            );
        }

        return view('understandings.result', compact('understanding', 'attempt'));
    }
}
