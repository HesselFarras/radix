<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ErdComponent;
use App\Models\ErdPuzzle;
use App\Models\ErdRelation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ErdPuzzleManagementController extends Controller
{
    public function index(): View
    {
        $puzzles = ErdPuzzle::with(['module', 'components', 'relations'])->latest()->get();

        return view('admin.erd-puzzles.index', compact('puzzles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'module_id' => ['required', 'exists:modules,id'], 'title' => ['required', 'string', 'max:255'],
            'case_text' => ['required', 'string'], 'instructions' => ['nullable', 'string'],
            'max_score' => ['required', 'integer', 'min:1'], 'xp_reward' => ['required', 'integer', 'min:0'],
        ]);
        $data['is_published'] = $request->boolean('is_published');
        ErdPuzzle::create($data);

        return back()->with('status', 'ERD Puzzle dibuat.');
    }

    public function update(Request $request, ErdPuzzle $puzzle): RedirectResponse
    {
        $data = $request->validate(['title' => ['required', 'string', 'max:255'], 'case_text' => ['required', 'string'], 'instructions' => ['nullable', 'string'], 'max_score' => ['required', 'integer', 'min:1'], 'xp_reward' => ['required', 'integer', 'min:0']]);
        $data['is_published'] = $request->boolean('is_published');
        $puzzle->update($data);

        return back()->with('status', 'ERD Puzzle diperbarui.');
    }

    public function updateComponent(Request $request, ErdPuzzle $puzzle, ErdComponent $component): RedirectResponse
    {
        abort_unless($component->erd_puzzle_id === $puzzle->id, 404);
        $data = $request->validate(['type' => ['required', 'in:entity,attribute'], 'name' => ['required', 'string', 'max:255'], 'definition_json' => ['nullable', 'json'], 'sort_order' => ['required', 'integer', 'min:1']]);
        try { $data['definition_json'] = $data['definition_json'] ? json_decode($data['definition_json'], true, 512, JSON_THROW_ON_ERROR) : null; } catch (\JsonException) { return back()->withErrors(['definition_json' => 'Definition JSON tidak valid.'])->withInput(); }
        $component->update($data);

        return back()->with('status', 'Komponen ERD diperbarui.');
    }

    public function updateRelation(Request $request, ErdPuzzle $puzzle, ErdRelation $relation): RedirectResponse
    {
        abort_unless($relation->erd_puzzle_id === $puzzle->id, 404);
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'definition_json' => ['required', 'json']]);
        try { $data['definition_json'] = $data['definition_json'] ? json_decode($data['definition_json'], true, 512, JSON_THROW_ON_ERROR) : null; } catch (\JsonException) { return back()->withErrors(['definition_json' => 'Definition JSON tidak valid.'])->withInput(); }
        $relation->update($data);

        return back()->with('status', 'Relasi ERD diperbarui.');
    }

    public function destroyComponent(ErdPuzzle $puzzle, ErdComponent $component): RedirectResponse
    {
        abort_unless($component->erd_puzzle_id === $puzzle->id, 404);
        DB::transaction(function () use ($component, $puzzle): void {
            $relations = $puzzle->relations()->get()->filter(function (ErdRelation $relation) use ($component): bool {
                return in_array($component->id, [data_get($relation->definition_json, 'from'), data_get($relation->definition_json, 'to')], true);
            });
            $relations->each->delete();
            $component->delete();
        });

        return back()->with('status', 'Komponen ERD dihapus beserta relasi terkait.');
    }

    public function destroyRelation(ErdPuzzle $puzzle, ErdRelation $relation): RedirectResponse
    {
        abort_unless($relation->erd_puzzle_id === $puzzle->id, 404);
        $relation->delete();

        return back()->with('status', 'Relasi ERD dihapus.');
    }

    public function storeComponent(Request $request, ErdPuzzle $puzzle): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:entity,attribute'], 'name' => ['required', 'string', 'max:255'],
            'definition_json' => ['nullable', 'json'], 'sort_order' => ['required', 'integer', 'min:1'],
        ]);
        try {
            $data['definition_json'] = $data['definition_json'] ? json_decode($data['definition_json'], true, 512, JSON_THROW_ON_ERROR) : null;
        } catch (\JsonException) {
            return back()->withErrors(['definition_json' => 'Definition JSON tidak valid.'])->withInput();
        }
        $data['erd_puzzle_id'] = $puzzle->id;
        ErdComponent::create($data);

        return back()->with('status', 'Komponen ERD dibuat.');
    }

    public function storeRelation(Request $request, ErdPuzzle $puzzle): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'definition_json' => ['required', 'json']]);
        try {
            $definition = json_decode($data['definition_json'], true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return back()->withErrors(['definition_json' => 'Definition JSON tidak valid.'])->withInput();
        }
        abort_unless(isset($definition['from'], $definition['to'], $definition['cardinality']) && $puzzle->components()->whereIn('id', [$definition['from'], $definition['to']])->count() === 2 && in_array($definition['cardinality'], ['1:1', '1:N', 'N:1', 'N:M'], true), 422, 'Definition relasi harus memiliki endpoint dan cardinality valid.');
        ErdRelation::create(['erd_puzzle_id' => $puzzle->id, 'name' => $data['name'], 'definition_json' => $definition]);

        return back()->with('status', 'Relasi ERD dibuat.');
    }
}
