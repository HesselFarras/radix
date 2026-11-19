<?php

namespace App\Http\Controllers;

use App\Models\Lab;
use Illuminate\View\View;

class SqlLabController extends Controller
{
    public function index(): View
    {
        $labs = Lab::query()->where('is_published', true)->with('module')->orderBy('sort_order')->get();

        return view('sql-lab.index', compact('labs'));
    }

    public function show(Lab $lab): View
    {
        abort_unless($lab->is_published, 404);
        $lab->load(['module', 'steps']);

        return view('sql-lab.show', compact('lab'));
    }
}
