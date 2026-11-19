<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Module;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModuleProgressTest extends TestCase
{
    use RefreshDatabase;

    public function test_next_module_stays_locked_until_previous_topics_are_complete_when_no_quiz_exists(): void
    {
        $user = User::factory()->create();
        $course = Course::create(['title' => 'Course', 'slug' => 'course', 'is_published' => true]);
        $first = Module::create(['course_id' => $course->id, 'title' => 'First', 'slug' => 'first', 'sort_order' => 1, 'is_published' => true]);
        $second = Module::create(['course_id' => $course->id, 'title' => 'Second', 'slug' => 'second', 'sort_order' => 2, 'is_published' => true]);
        $topic = Topic::create(['module_id' => $first->id, 'title' => 'Topik', 'slug' => 'topik', 'content_json' => ['blocks' => []], 'sort_order' => 1, 'is_published' => true]);

        $this->actingAs($user)->get(route('modules.show', $second))->assertForbidden();
        $user->topicProgress()->create(['topic_id' => $topic->id, 'completed_at' => now()]);
        $this->actingAs($user)->get(route('modules.show', $second))->assertOk();
    }
}
