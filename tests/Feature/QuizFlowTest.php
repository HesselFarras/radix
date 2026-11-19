<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Module;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_submit_quiz_and_receive_score(): void
    {
        $user = User::factory()->create();
        $course = Course::create(['title' => 'Course', 'slug' => 'course', 'is_published' => true]);
        $module = Module::create(['course_id' => $course->id, 'title' => 'Module', 'slug' => 'module', 'sort_order' => 1, 'is_published' => true]);
        $quiz = Quiz::create(['module_id' => $module->id, 'title' => 'Kuis', 'passing_score' => 70, 'max_xp' => 100, 'is_published' => true]);
        $question = Question::create(['quiz_id' => $quiz->id, 'question' => 'Benar?', 'points' => 1, 'sort_order' => 1]);
        $correct = QuestionOption::create(['question_id' => $question->id, 'option_text' => 'Ya', 'is_correct' => true, 'sort_order' => 1]);

        $this->actingAs($user)->post(route('quizzes.submit', $quiz), ['answers' => [$question->id => $correct->id]])->assertOk()->assertSee('100%');
        $this->assertDatabaseHas('quiz_attempts', ['user_id' => $user->id, 'quiz_id' => $quiz->id, 'score' => 100, 'passed' => true, 'xp_earned' => 100]);
        $this->assertDatabaseHas('xp_transactions', ['user_id' => $user->id, 'amount' => 100, 'source_type' => 'quiz_attempt']);
        $this->assertSame(100, $user->fresh()->total_xp);

        $this->actingAs($user)->post(route('quizzes.submit', $quiz), ['answers' => [$question->id => $correct->id]])->assertOk();
        $this->assertSame(200, $user->fresh()->total_xp);
    }
}
