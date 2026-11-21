<?php

namespace App\Http\Controllers;

use App\Models\Lab;
use App\Models\LabAttempt;
use App\Models\LabStep;
use App\Models\XpTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SqlLabController extends Controller
{
    public function index(): View
    {
        $labs = Lab::query()
            ->where('is_published', true)
            ->whereHas('module', fn ($query) => $query->where('is_published', true)->whereHas('course', fn ($course) => $course->where('is_published', true)))
            ->with(['module', 'steps', 'attempts' => fn ($query) => $query->where('user_id', auth()->id())->where('is_correct', true)->select('lab_attempts.id', 'lab_attempts.lab_step_id')])
            ->withCount('steps')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->each(fn (Lab $lab) => $lab->completed_steps_count = $lab->attempts->pluck('lab_step_id')->unique()->count());

        return view('sql-lab.index', compact('labs'));
    }

    public function show(Lab $lab): View
    {
        abort_unless($lab->is_published && $lab->module()->where('is_published', true)->whereHas('course', fn ($course) => $course->where('is_published', true))->exists(), 404);
        $lab->load(['module', 'steps']);
        $attempts = auth()->user()->labAttempts()
            ->whereIn('lab_step_id', $lab->steps->pluck('id'))
            ->latest('attempted_at')
            ->get()
            ->unique('lab_step_id')
            ->keyBy('lab_step_id');
        $attemptHistory = auth()->user()->labAttempts()
            ->whereIn('lab_step_id', $lab->steps->pluck('id'))
            ->latest('attempted_at')
            ->paginate(10, ['*'], 'attempts_page');

        return view('sql-lab.show', compact('lab', 'attempts', 'attemptHistory'));
    }

    public function saveDraft(Request $request, Lab $lab, LabStep $step): RedirectResponse
    {
        abort_unless($lab->is_published && $step->lab_id === $lab->id && $lab->module()->where('is_published', true)->whereHas('course', fn ($course) => $course->where('is_published', true))->exists(), 404);
        if (is_string($request->input('result_json'))) {
            $decoded = json_decode($request->input('result_json'), true);
            $request->merge(['result_json' => is_array($decoded) ? $decoded : null]);
        }

        $submitted = $request->validate([
            'query_text' => ['required', 'string', 'max:20000'],
            'result_json' => ['nullable', 'array'],
            'result_json.columns' => ['nullable', 'array'],
            'result_json.columns.*' => ['string', 'max:255'],
            'result_json.rows' => ['nullable', 'array'],
            'result_json.rows.*' => ['array'],
            'result_json.rows.*.*' => ['nullable', 'max:1000', function (string $attribute, mixed $value, \Closure $fail): void {
                if (is_array($value) || is_object($value)) {
                    $fail('Nilai hasil query harus scalar.');
                }
            }],
        ]);
        $expected = $step->expected_result_json;
        $actual = $submitted['result_json'] ?? null;
        $isCorrect = $lab->mode === 'challenge' && is_array($expected) && $actual !== null
            && $this->normalizeResult($actual) === $this->normalizeResult($expected);
        $xp = $isCorrect ? $step->xp_reward : 0;
        $attempt = DB::transaction(function () use ($step, $submitted, $actual, $isCorrect, $xp): LabAttempt {
            $attempt = auth()->user()->labAttempts()->create([
                'lab_step_id' => $step->id,
                'query_text' => $submitted['query_text'],
                'result_json' => $actual === null
                    ? ['status' => 'not_executed', 'message' => 'Menunggu hasil SQL.js.']
                    : ['status' => $isCorrect ? 'correct' : 'incorrect', 'result' => $actual],
                'is_correct' => $isCorrect,
                'xp_earned' => $xp,
                'attempted_at' => now(),
            ]);
            if ($xp > 0) {
                $transaction = XpTransaction::firstOrCreate(
                    ['idempotency_key' => 'lab-user-'.auth()->id().'-step-'.$step->id],
                    ['user_id' => auth()->id(), 'source_type' => 'lab_step', 'source_id' => $step->id, 'amount' => $xp, 'description' => 'XP dari '.$step->title],
                );
                if ($transaction->wasRecentlyCreated) {
                    auth()->user()->increment('total_xp', $xp);
                } else {
                    $xp = 0;
                    $attempt->update(['xp_earned' => 0]);
                }
            }

            return $attempt;
        });

        $status = match (true) {
            $actual === null => 'Draft disimpan, query belum dieksekusi.',
            $isCorrect => "Query benar! +$xp XP",
            default => 'Hasil belum sesuai: '.$this->resultDifference($actual, $expected),
        };

        return back()->with('draft_saved', $attempt->id)->with('status', $status);
    }

    private function resultDifference(array $actual, array $expected): string
    {
        $actualRows = $actual['rows'] ?? [];
        $expectedRows = $expected['rows'] ?? [];
        $sameRows = count(array_intersect(array_map('json_encode', $actualRows), array_map('json_encode', $expectedRows)));
        $columnsMatch = ($actual['columns'] ?? []) === ($expected['columns'] ?? []);
        $cellMismatches = 0;
        foreach ($expectedRows as $index => $expectedRow) {
            foreach ($expectedRow as $column => $value) {
                if (($actualRows[$index][$column] ?? null) != $value) $cellMismatches++;
            }
        }

        return $sameRows.'/'.count($expectedRows).' baris cocok'.($columnsMatch ? '' : '; kolom belum sesuai').($cellMismatches ? "; $cellMismatches nilai berbeda" : '');
    }

    private function normalizeResult(array $result): string
    {
        $rows = $result['rows'] ?? [];
        usort($rows, fn ($a, $b) => strcmp(json_encode($a), json_encode($b)));

        return json_encode(['columns' => array_values($result['columns'] ?? []), 'rows' => $rows]);
    }
}
