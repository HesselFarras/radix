<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Module;
use App\Models\Topic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function index(): View
    {
        $courses = Course::with('modules.topics')->orderBy('title')->get();

        return view('admin.content.index', compact('courses'));
    }

    public function storeTopic(Request $request, Module $module): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'alpha_dash', 'max:255', 'unique:topics,slug'],
            'content_json' => ['required', 'json'],
            'sort_order' => ['required', 'integer', 'min:1'],
            'estimated_minutes' => ['required', 'integer', 'min:1', 'max:999'],
            'is_published' => ['boolean'],
        ]);
        $data['content_json'] = json_decode($data['content_json'], true, 512, JSON_THROW_ON_ERROR);
        $data['module_id'] = $module->id;
        $data['is_published'] = $request->boolean('is_published');
        Topic::create($data);

        return back()->with('status', 'Topik dibuat.');
    }

    public function updateTopic(Request $request, Topic $topic): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content_json' => ['required', 'json'],
            'sort_order' => ['required', 'integer', 'min:1'],
            'estimated_minutes' => ['required', 'integer', 'min:1', 'max:999'],
            'is_published' => ['boolean'],
        ]);
        $data['content_json'] = json_decode($data['content_json'], true, 512, JSON_THROW_ON_ERROR);
        $data['is_published'] = $request->boolean('is_published');
        $topic->update($data);

        return back()->with('status', 'Topik diperbarui.');
    }

    public function destroyTopic(Topic $topic): RedirectResponse
    {
        $topic->delete();

        return back()->with('status', 'Topik dihapus.');
    }
}
