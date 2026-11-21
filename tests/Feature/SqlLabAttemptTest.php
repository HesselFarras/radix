<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Lab;
use App\Models\LabStep;
use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SqlLabAttemptTest extends TestCase
{
    use RefreshDatabase;

    private function createChallenge(): array
    {
        $course = Course::create(['title' => 'Course', 'slug' => 'course', 'is_published' => true]);
        $module = Module::create(['course_id' => $course->id, 'title' => 'Module', 'slug' => 'module', 'sort_order' => 1, 'is_published' => true]);
        $lab = Lab::create(['module_id' => $module->id, 'title' => 'Lab', 'mode' => 'challenge', 'is_published' => true]);
        $step = LabStep::create([
            'lab_id' => $lab->id,
            'title' => 'Step',
            'instruction' => 'Query',
            'sort_order' => 1,
            'expected_result_json' => ['columns' => ['id'], 'rows' => [[1]]],
            'xp_reward' => 25,
        ]);

        return [$lab, $step];
    }

    public function test_student_can_save_query_draft_without_execution_or_xp(): void
    {
        $user = User::factory()->create();
        $course = Course::create(['title' => 'Course', 'slug' => 'course', 'is_published' => true]);
        $module = Module::create(['course_id' => $course->id, 'title' => 'Module', 'slug' => 'module', 'sort_order' => 1, 'is_published' => true]);
        $lab = Lab::create(['module_id' => $module->id, 'title' => 'Lab', 'mode' => 'practice', 'is_published' => true]);
        $step = LabStep::create(['lab_id' => $lab->id, 'title' => 'Step', 'instruction' => 'Query', 'sort_order' => 1]);

        $this->actingAs($user)->post(route('sql-lab.attempts.store', [$lab, $step]), ['query_text' => 'SELECT 1;'])->assertRedirect();
        $this->assertDatabaseHas('lab_attempts', ['user_id' => $user->id, 'lab_step_id' => $step->id, 'is_correct' => false, 'xp_earned' => 0]);
        $this->assertSame(0, $user->fresh()->total_xp);
    }

    public function test_correct_lab_xp_is_awarded_once_per_user_and_step(): void
    {
        [$lab, $step] = $this->createChallenge();
        $user = User::factory()->create();
        $payload = ['query_text' => 'SELECT id FROM users;', 'result_json' => ['columns' => ['id'], 'rows' => [[1]]]];

        $this->actingAs($user)->post(route('sql-lab.attempts.store', [$lab, $step]), $payload)->assertRedirect();
        $this->actingAs($user)->post(route('sql-lab.attempts.store', [$lab, $step]), $payload)->assertRedirect();

        $this->assertSame(25, $user->fresh()->total_xp);
        $this->assertDatabaseCount('xp_transactions', 1);
    }

    public function test_result_payload_rejects_nested_objects(): void
    {
        [$lab, $step] = $this->createChallenge();
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('sql-lab.attempts.store', [$lab, $step]), [
            'query_text' => 'SELECT 1;',
            'result_json' => ['rows' => [[['unexpected' => 'object']]]],
        ])->assertSessionHasErrors('result_json.rows.0.0');
    }

    public function test_lab_xp_is_isolated_between_users(): void
    {
        [$lab, $step] = $this->createChallenge();
        $payload = ['query_text' => 'SELECT id FROM users;', 'result_json' => ['columns' => ['id'], 'rows' => [[1]]]];
        $first = User::factory()->create();
        $second = User::factory()->create();

        $this->actingAs($first)->post(route('sql-lab.attempts.store', [$lab, $step]), $payload);
        $this->actingAs($second)->post(route('sql-lab.attempts.store', [$lab, $step]), $payload);

        $this->assertSame(25, $first->fresh()->total_xp);
        $this->assertSame(25, $second->fresh()->total_xp);
        $this->assertDatabaseCount('xp_transactions', 2);
    }
}
