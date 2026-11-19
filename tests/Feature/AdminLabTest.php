<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Lab;
use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLabTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_lab_and_step(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $course = Course::create(['title' => 'Course', 'slug' => 'course', 'is_published' => true]);
        $module = Module::create(['course_id' => $course->id, 'title' => 'Module', 'slug' => 'module', 'sort_order' => 1, 'is_published' => true]);

        $this->actingAs($admin)->post(route('admin.labs.store'), ['module_id' => $module->id, 'title' => 'Library Lab', 'mode' => 'practice', 'schema_json' => '{"tables":[]}', 'sort_order' => 1])->assertRedirect();
        $lab = Lab::firstOrFail();
        $this->actingAs($admin)->post(route('admin.labs.steps.store', $lab), ['title' => 'Step 1', 'instruction' => 'Run query', 'expected_result_json' => '{"rows":[]}', 'xp_reward' => 20, 'sort_order' => 1])->assertRedirect();
        $this->assertDatabaseHas('lab_steps', ['lab_id' => $lab->id, 'title' => 'Step 1']);
    }

    public function test_student_cannot_access_lab_management(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'student']))->get(route('admin.labs.index'))->assertForbidden();
    }
}
