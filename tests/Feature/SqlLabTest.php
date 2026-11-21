<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Lab;
use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SqlLabTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_open_published_sql_lab(): void
    {
        $user = User::factory()->create();
        $course = Course::create(['title' => 'Course', 'slug' => 'course', 'is_published' => true]);
        $module = Module::create(['course_id' => $course->id, 'title' => 'Module', 'slug' => 'module', 'sort_order' => 1, 'is_published' => true]);
        $lab = Lab::create(['module_id' => $module->id, 'title' => 'Library Lab', 'mode' => 'practice', 'schema_json' => ['tables' => []], 'is_published' => true]);

        $this->actingAs($user)->get(route('sql-lab.index'))->assertOk()->assertSee($lab->title);
        $this->actingAs($user)->get(route('sql-lab.show', $lab))->assertOk()->assertSee('SQL.js');
    }

    public function test_unpublished_sql_lab_is_not_visible_or_openable(): void
    {
        $user = User::factory()->create();
        $course = Course::create(['title' => 'Course', 'slug' => 'course-hidden', 'is_published' => true]);
        $module = Module::create(['course_id' => $course->id, 'title' => 'Module', 'slug' => 'module-hidden', 'sort_order' => 1, 'is_published' => true]);
        $lab = Lab::create(['module_id' => $module->id, 'title' => 'Hidden Lab', 'mode' => 'practice', 'schema_json' => ['tables' => []], 'is_published' => false]);

        $this->actingAs($user)->get(route('sql-lab.index'))->assertOk()->assertDontSee($lab->title);
        $this->actingAs($user)->get(route('sql-lab.show', $lab))->assertNotFound();
    }

    public function test_lab_under_unpublished_module_is_hidden(): void
    {
        $user = User::factory()->create();
        $course = Course::create(['title' => 'Course', 'slug' => 'hidden-parent', 'is_published' => true]);
        $module = Module::create(['course_id' => $course->id, 'title' => 'Hidden Module', 'slug' => 'hidden-parent-module', 'sort_order' => 1, 'is_published' => false]);
        $lab = Lab::create(['module_id' => $module->id, 'title' => 'Hidden Parent Lab', 'mode' => 'practice', 'schema_json' => ['tables' => []], 'is_published' => true]);

        $this->actingAs($user)->get(route('sql-lab.index'))->assertOk()->assertDontSee($lab->title);
        $this->actingAs($user)->get(route('sql-lab.show', $lab))->assertNotFound();
    }

    public function test_repeated_correct_attempts_do_not_duplicate_progress(): void
    {
        $user = User::factory()->create();
        $course = Course::create(['title' => 'Course', 'slug' => 'progress-course', 'is_published' => true]);
        $module = Module::create(['course_id' => $course->id, 'title' => 'Module', 'slug' => 'progress-module', 'sort_order' => 1, 'is_published' => true]);
        $lab = Lab::create(['module_id' => $module->id, 'title' => 'Progress Lab', 'mode' => 'challenge', 'schema_json' => ['tables' => []], 'is_published' => true]);
        $step = $lab->steps()->create(['title' => 'Step', 'instruction' => 'Run query', 'expected_result_json' => ['columns' => ['x'], 'rows' => [[1]]], 'reference_query' => 'SELECT 1', 'xp_reward' => 1, 'sort_order' => 1]);
        $payload = ['query_text' => 'SELECT 1', 'result_json' => ['columns' => ['x'], 'rows' => [[1]]]];

        $this->actingAs($user)->post(route('sql-lab.attempts.store', [$lab, $step]), $payload)->assertRedirect();
        $this->actingAs($user)->post(route('sql-lab.attempts.store', [$lab, $step]), $payload)->assertRedirect();
        $this->actingAs($user)->get(route('sql-lab.index'))->assertSee('Selesai');
    }

    public function test_sql_lab_schema_is_rendered_for_the_browser_engine(): void
    {
        $user = User::factory()->create();
        $course = Course::create(['title' => 'Course', 'slug' => 'course', 'is_published' => true]);
        $module = Module::create(['course_id' => $course->id, 'title' => 'Module', 'slug' => 'module', 'sort_order' => 1, 'is_published' => true]);
        $lab = Lab::create(['module_id' => $module->id, 'title' => 'Library Lab', 'mode' => 'challenge', 'schema_json' => ['tables' => [['name' => 'students', 'columns' => [['name' => 'id', 'type' => 'INTEGER']], 'rows' => [[1]]]]], 'is_published' => true]);

        $this->actingAs($user)->get(route('sql-lab.show', $lab))
            ->assertOk()
            ->assertSee('SQL.js')
            ->assertSee('students');
    }
}
