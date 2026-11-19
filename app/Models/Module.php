<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Module extends Model
{
    protected $fillable = [
        'course_id', 'title', 'slug', 'description',
        'sort_order', 'estimated_minutes', 'is_published',
    ];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class)->orderBy('sort_order');
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function labs(): HasMany
    {
        return $this->hasMany(Lab::class)->orderBy('sort_order');
    }

    public function isCompletedBy(User $user, Collection $completedTopicIds, Collection $passedQuizIds): bool
    {
        $topicsDone = $this->topics->isNotEmpty() && $this->topics->every(fn (Topic $topic) => $completedTopicIds->contains($topic->id));
        $quizDone = $this->quizzes->isEmpty() || $this->quizzes->every(fn (Quiz $quiz) => $passedQuizIds->contains($quiz->id));

        return $topicsDone && $quizDone;
    }

    public function isUnlockedFor(User $user, Collection $completedTopicIds, Collection $passedQuizIds): bool
    {
        $previous = $this->course->modules->firstWhere('sort_order', $this->sort_order - 1);

        return $previous === null || $previous->isCompletedBy($user, $completedTopicIds, $passedQuizIds);
    }
}
