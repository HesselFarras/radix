<?php

namespace Tests\Feature;

use App\Models\Badge;
use App\Models\Course;
use App\Models\Module;
use App\Models\Topic;
use App\Models\User;
use App\Services\BadgeEvaluator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BadgeProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_displays_user_and_evaluator_awards_first_module_badge(): void
    {
        $user = User::factory()->create();
        $course = Course::create(['title' => 'Course', 'slug' => 'course', 'is_published' => true]);
        $module = Module::create(['course_id' => $course->id, 'title' => 'Module', 'slug' => 'module', 'sort_order' => 1, 'is_published' => true]);
        $topic = Topic::create(['module_id' => $module->id, 'title' => 'Topik', 'slug' => 'topik', 'content_json' => ['blocks' => []], 'sort_order' => 1, 'is_published' => true]);
        $user->enrollments()->create(['course_id' => $course->id]);
        $user->topicProgress()->create(['topic_id' => $topic->id, 'completed_at' => now()]);
        Badge::create(['name' => 'Langkah Pertama', 'description' => 'Modul pertama.', 'criteria_type' => 'first_module', 'is_active' => true]);

        app(BadgeEvaluator::class)->evaluate($user);
        $this->assertDatabaseHas('user_badges', ['user_id' => $user->id]);
        $this->actingAs($user)->get(route('profile.show'))->assertOk()->assertSee($user->name)->assertSee('Langkah Pertama');
    }
}
