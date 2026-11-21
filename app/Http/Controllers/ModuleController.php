<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\View\View;

class ModuleController extends Controller
{
    public function show(Module $module): View
    {
        abort_unless($module->is_published && $module->course()->where('is_published', true)->exists(), 404);

        $user = auth()->user();
        $module->load(['course.modules' => fn ($query) => $query->where('is_published', true), 'course.modules.topics' => fn ($query) => $query->where('is_published', true), 'course.modules.quizzes' => fn ($query) => $query->where('is_published', true), 'topics' => fn ($query) => $query->where('is_published', true), 'quizzes' => fn ($query) => $query->where('is_published', true)]);
        $completedTopicIds = $user->topicProgress()
            ->whereIn('topic_id', $module->course->modules->flatMap->topics->pluck('id'))
            ->whereNotNull('completed_at')
            ->pluck('topic_id');
        $passedQuizIds = $user->quizAttempts()->where('passed', true)->pluck('quiz_id')->unique();
        abort_unless($module->isUnlockedFor($user, $completedTopicIds, $passedQuizIds), 403);

        return view('modules.show', compact('module', 'completedTopicIds', 'passedQuizIds'));
    }
}
