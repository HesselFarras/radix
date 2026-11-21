<?php

namespace App\Http\Controllers;

use App\Models\ErdAttempt;
use App\Models\ErdPuzzle;
use App\Models\XpTransaction;
use App\Services\BadgeEvaluator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ErdPuzzleController extends Controller
{
    public function index(): View
    {
        $puzzles = ErdPuzzle::where('is_published', true)->whereHas('module', fn ($query) => $query->where('is_published', true)->whereHas('course', fn ($course) => $course->where('is_published', true)))->with('module')->latest()->get();

        return view('erd-puzzle.index', compact('puzzles'));
    }

    public function show(ErdPuzzle $puzzle): View
    {
        abort_unless($puzzle->is_published && $puzzle->module()->where('is_published', true)->whereHas('course', fn ($course) => $course->where('is_published', true))->exists(), 404);
        $puzzle->load(['module', 'components', 'relations']);
        $history = $puzzle->attempts()->where('user_id', auth()->id())->latest('completed_at')->paginate(10, ['*'], 'attempts_page');

        return view('erd-puzzle.show', compact('puzzle', 'history'));
    }

    public function submit(Request $request, ErdPuzzle $puzzle, BadgeEvaluator $badges): RedirectResponse
    {
        abort_unless($puzzle->is_published && $puzzle->module()->where('is_published', true)->whereHas('course', fn ($course) => $course->where('is_published', true))->exists(), 404);
        $puzzle->load(['components', 'relations']);
        $history = $puzzle->attempts()->where('user_id', auth()->id())->latest('completed_at')->get();
        $data = $request->validate([
            'components' => ['array'],
            'components.*' => ['integer', Rule::exists('erd_components', 'id')->where('erd_puzzle_id', $puzzle->id)],
            'relations' => ['array'],
            'relations.*' => ['integer', Rule::exists('erd_relations', 'id')->where('erd_puzzle_id', $puzzle->id)],
            'relation_cardinality' => ['array'],
            'relation_cardinality.*' => ['nullable', 'string', Rule::in(['1:1', '1:N', 'N:1', 'N:M'])],
        ]);

        $expectedComponents = $puzzle->components->pluck('id')->sort()->values()->all();
        $selectedComponents = collect($data['components'] ?? [])->map(fn ($id) => (int) $id)->sort()->values()->all();
        $expectedRelations = $puzzle->relations->pluck('id')->sort()->values()->all();
        $selectedRelations = collect($data['relations'] ?? [])->map(fn ($id) => (int) $id)->sort()->values()->all();
        $relationFeedback = $puzzle->relations->mapWithKeys(fn ($relation) => [$relation->id => ['name' => $relation->name, 'cardinality' => data_get($relation->definition_json, 'cardinality', '1:N'), 'selected' => in_array($relation->id, $selectedRelations, true)]]);
        $componentScore = $expectedComponents ? count(array_intersect($expectedComponents, $selectedComponents)) / count($expectedComponents) : 0;
        $componentFeedback = $puzzle->components->map(fn ($component) => ['name' => $component->name, 'selected' => in_array($component->id, $selectedComponents, true)])->values()->all();
        $componentPenalty = max(0, count($selectedComponents) - count($expectedComponents));
        $submittedCardinalities = $data['relation_cardinality'] ?? [];
        $cardinalityCorrect = $puzzle->relations->filter(fn ($relation) => ($submittedCardinalities[$relation->id] ?? null) === data_get($relation->definition_json, 'cardinality'))->count();
        $relationScore = $expectedRelations ? ((count(array_intersect($expectedRelations, $selectedRelations)) + $cardinalityCorrect) / (count($expectedRelations) * 2)) : 1;
        $relationPenalty = max(0, count($selectedRelations) - count($expectedRelations));
        $score = (int) round(max(0, (($componentScore + $relationScore) / 2) - (($componentPenalty + $relationPenalty) * 0.1)) * $puzzle->max_score);
        $passed = $score >= 70;
        $xp = $passed ? $puzzle->xp_reward : 0;
        $feedback = [
            'components_correct' => count(array_intersect($expectedComponents, $selectedComponents)),
            'components_total' => count($expectedComponents),
            'components' => $componentFeedback,
            'relations_correct' => count(array_intersect($expectedRelations, $selectedRelations)),
            'relations_total' => count($expectedRelations),
            'cardinality_correct' => $cardinalityCorrect,
            'relations' => $relationFeedback->map(fn ($item, $id) => [...$item, 'cardinality_selected' => $submittedCardinalities[$id] ?? null, 'cardinality_correct' => ($submittedCardinalities[$id] ?? null) === $item['cardinality']])->values()->all(),
        ];
        $attempt = DB::transaction(function () use ($puzzle, $data, $score, $passed, $xp, $feedback) {
            $number = ErdAttempt::where('user_id', auth()->id())->where('erd_puzzle_id', $puzzle->id)->count() + 1;
            $attempt = $puzzle->attempts()->create([
                'user_id' => auth()->id(), 'answer_json' => [...$data, 'feedback' => $feedback], 'score' => $score,
                'passed' => $passed, 'xp_earned' => $xp, 'attempt_number' => $number,
                'completed_at' => now(),
            ]);
            if ($xp > 0) {
                $transaction = XpTransaction::firstOrCreate(['idempotency_key' => 'erd-user-'.auth()->id().'-'.$puzzle->id], [
                    'user_id' => auth()->id(), 'source_type' => 'erd_puzzle', 'source_id' => $puzzle->id,
                    'amount' => $xp, 'description' => 'XP dari '.$puzzle->title,
                ]);
                if ($transaction->wasRecentlyCreated) {
                    auth()->user()->increment('total_xp', $xp);
                }
            }

            return $attempt;
        });

        $badges->evaluate(auth()->user());

        return redirect()->route('erd-puzzle.show', $puzzle)->with('result', $attempt);
    }
}
