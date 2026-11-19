<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BadgeManagementController extends Controller
{
    private const CRITERIA = ['first_module', 'half_course', 'course_complete', 'first_quiz_pass', 'perfect_erd', 'xp'];

    public function index(): View
    {
        $badges = Badge::latest()->get();

        return view('admin.badges.index', compact('badges'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'], 'description' => ['required', 'string'],
            'criteria_type' => ['required', 'in:'.implode(',', self::CRITERIA)],
            'criteria_value' => ['nullable', 'integer', 'min:0'], 'icon' => ['nullable', 'string', 'max:20'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        Badge::create($data);

        return back()->with('status', 'Badge dibuat.');
    }

    public function update(Request $request, Badge $badge): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'], 'description' => ['required', 'string'],
            'criteria_type' => ['required', 'in:'.implode(',', self::CRITERIA)],
            'criteria_value' => ['nullable', 'integer', 'min:0'], 'icon' => ['nullable', 'string', 'max:20'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $badge->update($data);

        return back()->with('status', 'Badge diperbarui.');
    }

    public function destroy(Badge $badge): RedirectResponse
    {
        $badge->delete();

        return back()->with('status', 'Badge dihapus.');
    }
}
