<?php

namespace App\Http\Controllers;

use App\Models\Annotation;
use App\Models\Topic;
use App\Models\TopicProgress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TopicController extends Controller
{
    public function show(Topic $topic): View
    {
        abort_unless($topic->is_published, 404);

        $topic->load(['module.course', 'module.topics']);
        $progress = auth()->user()->topicProgress()->firstOrCreate(
            ['topic_id' => $topic->id],
            ['last_read_at' => now()],
        );
        $progress->update(['last_read_at' => now()]);
        $annotations = auth()->user()->annotations()
            ->where('topic_id', $topic->id)
            ->latest()
            ->get();

        return view('topics.show', compact('topic', 'progress', 'annotations'));
    }

    public function complete(Topic $topic): RedirectResponse
    {
        abort_unless($topic->is_published, 404);

        TopicProgress::updateOrCreate(
            ['user_id' => auth()->id(), 'topic_id' => $topic->id],
            ['completed_at' => now(), 'last_read_at' => now()],
        );

        return redirect()->route('topics.show', $topic)->with('status', 'Topik ditandai selesai.');
    }

    public function annotate(Request $request, Topic $topic): RedirectResponse
    {
        abort_unless($topic->is_published, 404);

        $data = $request->validate([
            'type' => ['required', 'in:highlight,bookmark'],
            'selected_text' => ['nullable', 'string', 'max:5000'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        auth()->user()->annotations()->create([
            ...$data,
            'topic_id' => $topic->id,
        ]);

        return redirect()->route('topics.show', $topic)->with('status', 'Catatan disimpan.');
    }

    public function deleteAnnotation(Annotation $annotation): RedirectResponse
    {
        abort_unless($annotation->user_id === auth()->id(), 403);
        $topic = $annotation->topic;
        $annotation->delete();

        return redirect()->route('topics.show', $topic)->with('status', 'Catatan dihapus.');
    }
}
