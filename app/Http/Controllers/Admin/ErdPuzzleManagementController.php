<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ErdComponent;
use App\Models\ErdPuzzle;
use App\Models\ErdRelation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function storeComponent(Request $request, ErdPuzzle $puzzle): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:entity,attribute'], 'name' => ['required', 'string', 'max:255'],
            'definition_json' => ['nullable', 'json'], 'sort_order' => ['required', 'integer', 'min:1'],
        ]);
        $data['definition_json'] = $data['definition_json'] ? json_decode($data['definition_json'], true, 512, JSON_THROW_ON_ERROR) : null;
        $data['erd_puzzle_id'] = $puzzle->id;
        ErdComponent::create($data);

        return back()->with('status', 'Komponen ERD dibuat.');
    }

    public function storeRelation(Request $request, ErdPuzzle $puzzle): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'definition_json' => ['nullable', 'json']]);
        $data['definition_json'] = $data['definition_json'] ? json_decode($data['definition_json'], true, 512, JSON_THROW_ON_ERROR) : null;
        $data['erd_puzzle_id'] = $puzzle->id;
        ErdRelation::create($data);

        return back()->with('status', 'Relasi ERD dibuat.');
    }
}
