<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();
        $course = Course::where('slug', 'basis-data')->with(['modules.topics', 'modules.quizzes'])->first();
        $completedTopicIds = $user->topicProgress()->whereNotNull('completed_at')->pluck('topic_id');
        $passedQuizIds = $user->quizAttempts()->where('passed', true)->pluck('quiz_id')->unique();
        $user->load('badges');

        return view('profile.show', compact('user', 'course', 'completedTopicIds', 'passedQuizIds'));
    }
}
