<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Module;
use App\Models\Topic;
use App\Models\Understanding;
use App\Models\UnderstandingOption;
use App\Models\UnderstandingQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnderstandingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_submit_understanding_and_get_score(): void
    {
        $user = User::factory()->create();
        $course = Course::create(['title' => 'Course', 'slug' => 'course', 'is_published' => true]);
        $module = Module::create(['course_id' => $course->id, 'title' => 'Module', 'slug' => 'module', 'sort_order' => 1, 'is_published' => true]);
        $topic = Topic::create(['module_id' => $module->id, 'title' => 'Topik', 'slug' => 'topik', 'content_json' => ['blocks' => []], 'sort_order' => 1, 'is_published' => true]);
        $understanding = Understanding::create(['topic_id' => $topic->id, 'title' => 'Cek', 'passing_score' => 70]);
        $question = UnderstandingQuestion::create(['understanding_id' => $understanding->id, 'question' => 'Benar?', 'points' => 1, 'sort_order' => 1]);
        $correct = UnderstandingOption::create(['understanding_question_id' => $question->id, 'option_text' => 'Ya', 'is_correct' => true, 'sort_order' => 1]);
        UnderstandingOption::create(['understanding_question_id' => $question->id, 'option_text' => 'Tidak', 'is_correct' => false, 'sort_order' => 2]);

        $this->actingAs($user)
            ->post(route('understandings.submit', $understanding), ['answers' => [$question->id => $correct->id]])
            ->assertOk()
            ->assertSee('100%');
        $this->assertDatabaseHas('understanding_attempts', ['user_id' => $user->id, 'understanding_id' => $understanding->id, 'score' => 100, 'passed' => true]);
    }
}
