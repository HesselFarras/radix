<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function show(): View
    {
        $user = auth()->user();
        $course = Course::where('slug', 'basis-data')->where('is_published', true)->with(['modules' => fn ($query) => $query->where('is_published', true)->with(['topics' => fn ($topics) => $topics->where('is_published', true), 'quizzes' => fn ($quizzes) => $quizzes->where('is_published', true)])])->firstOrFail();
        $completedTopicIds = $user->topicProgress()->whereNotNull('completed_at')->pluck('topic_id');
        $passedQuizIds = $user->quizAttempts()->where('passed', true)->pluck('quiz_id')->unique();
        $completedModules = $course->modules->filter(fn ($module) => $module->isCompletedBy($user, $completedTopicIds, $passedQuizIds));
        $progress = $course->modules->count() ? (int) round($completedModules->count() / $course->modules->count() * 100) : 0;

        abort_unless($progress === 100, 403, 'Sertifikat belum tersedia.');

        return view('certificate.show', compact('user', 'course', 'progress'));
    }
}
