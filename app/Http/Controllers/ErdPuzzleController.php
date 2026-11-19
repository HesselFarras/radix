<?php

namespace App\Http\Controllers;

use App\Models\ErdAttempt;
use App\Models\ErdPuzzle;
use App\Models\XpTransaction;
use App\Services\BadgeEvaluator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ErdPuzzleController extends Controller
{
    public function index(): View
    {
        $puzzles = ErdPuzzle::where('is_published', true)->with('module')->latest()->get();

        return view('erd-puzzle.index', compact('puzzles'));
    }

    public function show(ErdPuzzle $puzzle): View
    {
        abort_unless($puzzle->is_published, 404);
        $puzzle->load(['module', 'components', 'relations']);

        return view('erd-puzzle.show', compact('puzzle'));
    }

    public function submit(Request $request, ErdPuzzle $puzzle, BadgeEvaluator $badges): RedirectResponse
    {
        abort_unless($puzzle->is_published, 404);
        $puzzle->load(['components', 'relations']);
        $data = $request->validate([
            'components' => ['array'],
            'components.*' => ['integer', 'exists:erd_components,id'],
            'relations' => ['array'],
            'relations.*' => ['integer', 'exists:erd_relations,id'],
        ]);

        $expectedComponents = $puzzle->components->pluck('id')->sort()->values()->all();
        $selectedComponents = collect($data['components'] ?? [])->map(fn ($id) => (int) $id)->sort()->values()->all();
        $expectedRelations = $puzzle->relations->pluck('id')->sort()->values()->all();
        $selectedRelations = collect($data['relations'] ?? [])->map(fn ($id) => (int) $id)->sort()->values()->all();
        $componentScore = $expectedComponents ? count(array_intersect($expectedComponents, $selectedComponents)) / count($expectedComponents) : 0;
        $relationScore = $expectedRelations ? count(array_intersect($expectedRelations, $selectedRelations)) / count($expectedRelations) : 1;
        $score = (int) round(($componentScore + $relationScore) / 2 * $puzzle->max_score);
        $passed = $score >= 70;
        $xp = $passed ? $puzzle->xp_reward : 0;
        $attempt = DB::transaction(function () use ($puzzle, $data, $score, $passed, $xp) {
            $number = ErdAttempt::where('user_id', auth()->id())->where('erd_puzzle_id', $puzzle->id)->count() + 1;
            $attempt = $puzzle->attempts()->create([
                'user_id' => auth()->id(), 'answer_json' => $data, 'score' => $score,
                'passed' => $passed, 'xp_earned' => $xp, 'attempt_number' => $number,
                'completed_at' => now(),
            ]);
            if ($xp > 0) {
                $transaction = XpTransaction::firstOrCreate(['idempotency_key' => 'erd-puzzle-'.$puzzle->id], [
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
