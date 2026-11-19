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
        $this->actingAs($user)->get(route('sql-lab.show', $lab))->assertOk()->assertSee('menunggu engine SQL');
    }
}
