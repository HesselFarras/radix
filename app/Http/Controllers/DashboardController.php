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
            ->with(['modules.topics', 'modules.quizzes', 'modules.labs.steps', 'modules.erdPuzzles'])
            ->first();

        $user = auth()->user();
        $completedTopicIds = $user->topicProgress()
            ->whereNotNull('completed_at')
            ->pluck('topic_id');
        $completedTopicIds = $completedTopicIds->merge(
            $user->understandingAttempts()
                ->where('passed', true)
                ->join('understandings', 'understanding_attempts.understanding_id', '=', 'understandings.id')
                ->pluck('understandings.topic_id')
        )->unique()->values();
        $passedQuizIds = $user->quizAttempts()
            ->where('passed', true)
            ->pluck('quiz_id')
            ->unique();
        $labStepCount = $course?->modules->flatMap->labs->flatMap->steps->count() ?? 0;
        $completedLabSteps = $user->labAttempts()->where('is_correct', true)->distinct('lab_step_id')->count('lab_step_id');
        $erdCount = $course?->modules->flatMap->erdPuzzles->count() ?? 0;
        $completedErd = $user->erdAttempts()->where('passed', true)->distinct('erd_puzzle_id')->count('erd_puzzle_id');

        $activityDates = collect()
            ->merge($user->topicProgress()->whereNotNull('completed_at')->pluck('completed_at'))
            ->merge($user->understandingAttempts()->whereNotNull('completed_at')->pluck('completed_at'))
            ->merge($user->quizAttempts()->whereNotNull('completed_at')->pluck('completed_at'))
            ->merge($user->labAttempts()->whereNotNull('attempted_at')->pluck('attempted_at'))
            ->merge($user->erdAttempts()->whereNotNull('completed_at')->pluck('completed_at'))
            ->map(fn ($date) => \Carbon\Carbon::parse($date)->toDateString())
            ->unique()
            ->sort()
            ->values();
        $streak = 0;
        $cursor = now()->startOfDay();
        if ($activityDates->isNotEmpty() && $activityDates->last() === $cursor->toDateString()) {
            while ($activityDates->contains($cursor->toDateString())) {
                $streak++;
                $cursor->subDay();
            }
        }

        return view('dashboard', [
            'course' => $course,
            'modules' => $course?->modules ?? collect(),
            'completedTopicIds' => $completedTopicIds,
            'passedQuizIds' => $passedQuizIds,
            'totalXp' => $user->total_xp,
            'streak' => $streak,
            'labProgress' => [$completedLabSteps, $labStepCount],
            'erdProgress' => [$completedErd, $erdCount],
        ]);
    }
}
