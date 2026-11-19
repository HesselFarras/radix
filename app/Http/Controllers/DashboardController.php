<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $course = Course::query()
            ->where('slug', 'basis-data')
            ->where('is_published', true)
            ->with(['modules.topics', 'modules.quizzes'])
            ->first();

        $user = auth()->user();
        $completedTopicIds = $user->topicProgress()
            ->whereNotNull('completed_at')
            ->pluck('topic_id');
        $passedQuizIds = $user->quizAttempts()
            ->where('passed', true)
            ->pluck('quiz_id')
            ->unique();

        return view('dashboard', [
            'course' => $course,
            'completedTopicIds' => $completedTopicIds,
            'passedQuizIds' => $passedQuizIds,
            'totalXp' => $user->total_xp,
        ]);
    }
}
