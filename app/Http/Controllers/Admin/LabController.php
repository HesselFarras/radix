<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lab;
use App\Models\LabStep;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LabController extends Controller
{
    public function index(): View
    {
        $labs = Lab::with(['module', 'steps'])->latest()->get();

        return view('admin.labs.index', compact('labs'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'module_id' => ['required', 'exists:modules,id'], 'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'], 'mode' => ['required', 'in:practice,challenge'],
            'schema_json' => ['nullable', 'json'], 'sort_order' => ['required', 'integer', 'min:1'],
        ]);
        $data['schema_json'] = $data['schema_json'] ? json_decode($data['schema_json'], true, 512, JSON_THROW_ON_ERROR) : null;
        $data['is_published'] = $request->boolean('is_published');
        Lab::create($data);

        return back()->with('status', 'SQL Lab dibuat.');
    }

    public function storeStep(Request $request, Lab $lab): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'], 'instruction' => ['required', 'string'],
            'expected_result_json' => ['nullable', 'json'], 'reference_query' => ['nullable', 'string'],
            'xp_reward' => ['required', 'integer', 'min:0'], 'sort_order' => ['required', 'integer', 'min:1'],
        ]);
        $data['expected_result_json'] = $data['expected_result_json'] ? json_decode($data['expected_result_json'], true, 512, JSON_THROW_ON_ERROR) : null;
        $data['lab_id'] = $lab->id;
        LabStep::create($data);

        return back()->with('status', 'Langkah Lab dibuat.');
    }
}
