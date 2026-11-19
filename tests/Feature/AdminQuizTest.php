<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Module;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminQuizTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_quiz_question_and_option(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $course = Course::create(['title' => 'Course', 'slug' => 'course', 'is_published' => true]);
        $module = Module::create(['course_id' => $course->id, 'title' => 'Module', 'slug' => 'module', 'sort_order' => 1, 'is_published' => true]);

        $this->actingAs($admin)->post(route('admin.quizzes.store'), ['module_id' => $module->id, 'title' => 'Kuis Modul', 'passing_score' => 70, 'max_xp' => 100])->assertRedirect();
        $quiz = Quiz::firstOrFail();
        $this->actingAs($admin)->post(route('admin.quizzes.questions.store', $quiz), ['type' => 'multiple_choice', 'question' => 'Benar?', 'points' => 1, 'sort_order' => 1])->assertRedirect();
        $question = Question::firstOrFail();
        $this->actingAs($admin)->post(route('admin.questions.options.store', $question), ['option_text' => 'Ya', 'sort_order' => 1, 'is_correct' => 1])->assertRedirect();
        $this->assertDatabaseHas('question_options', ['question_id' => $question->id, 'is_correct' => true]);
    }

    public function test_student_cannot_access_quiz_management(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'student']))->get(route('admin.quizzes.index'))->assertForbidden();
    }
}
