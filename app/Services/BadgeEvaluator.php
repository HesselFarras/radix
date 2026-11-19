<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\User;

class BadgeEvaluator
{
    public function evaluate(User $user): void
    {
        $course = $user->enrollments()->with('course.modules.topics', 'course.modules.quizzes')->first()?->course;
        if (! $course) {
            return;
        }

        $completedTopicIds = $user->topicProgress()->whereNotNull('completed_at')->pluck('topic_id');
        $passedQuizIds = $user->quizAttempts()->where('passed', true)->pluck('quiz_id')->unique();
        $completedModules = $course->modules->filter(fn ($module) => $module->isCompletedBy($user, $completedTopicIds, $passedQuizIds));

        $firstPassedQuiz = $user->quizAttempts()->where('passed', true)->exists();

        foreach (Badge::where('is_active', true)->get() as $badge) {
            $matches = match ($badge->criteria_type) {
                'first_module' => $completedModules->count() >= 1,
                'half_course' => $completedModules->count() >= 2,
                'course_complete' => $course->modules->isNotEmpty() && $completedModules->count() === $course->modules->count(),
                'first_quiz_pass' => $firstPassedQuiz,
                'perfect_erd' => $user->erdAttempts()->where('passed', true)->where('score', 100)->exists(),
                'xp' => $user->total_xp >= $badge->criteria_value,
                default => false,
            };

            if ($matches) {
                $user->badges()->syncWithoutDetaching([$badge->id => ['awarded_at' => now()]]);
            }
        }
    }
}
