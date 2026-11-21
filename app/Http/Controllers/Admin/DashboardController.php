<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Module;
use App\Models\QuizAttempt;
use App\Models\TopicProgress;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $course = Course::where('slug', 'basis-data')->with('modules.topics')->first();
        $moduleCount = Module::where('is_published', true)->count();
        $activeUsers = User::where('is_active', true)->where('role', 'student')->count();
        $averageQuizScore = round((float) (QuizAttempt::avg('score') ?? 0), 1);
        $totalTopics = $course?->modules->flatMap->topics->count() ?? 0;
        $completedTopics = TopicProgress::whereNotNull('completed_at')
            ->select(['user_id', 'topic_id'])
            ->groupBy(['user_id', 'topic_id'])
            ->get()
            ->count();
        $completionRate = $totalTopics && $activeUsers ? round($completedTopics / ($totalTopics * $activeUsers) * 100, 1) : 0;
        $recentAttempts = QuizAttempt::with(['user', 'quiz'])->latest()->limit(5)->get();

        return view('admin.dashboard', compact('activeUsers', 'moduleCount', 'averageQuizScore', 'completionRate', 'recentAttempts'));
    }
}
