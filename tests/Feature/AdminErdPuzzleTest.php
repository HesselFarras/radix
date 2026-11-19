<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\ErdPuzzle;
use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminErdPuzzleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_puzzle_and_component(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $course = Course::create(['title' => 'Course', 'slug' => 'course', 'is_published' => true]);
        $module = Module::create(['course_id' => $course->id, 'title' => 'Module', 'slug' => 'module', 'sort_order' => 1, 'is_published' => true]);

        $this->actingAs($admin)->post(route('admin.erd-puzzles.store'), ['module_id' => $module->id, 'title' => 'Perpustakaan', 'case_text' => 'Case', 'max_score' => 100, 'xp_reward' => 25])->assertRedirect();
        $puzzle = ErdPuzzle::firstOrFail();
        $this->actingAs($admin)->post(route('admin.erd-puzzles.components.store', $puzzle), ['type' => 'entity', 'name' => 'Buku', 'definition_json' => '{"key":"value"}', 'sort_order' => 1])->assertRedirect();
        $this->assertDatabaseHas('erd_components', ['erd_puzzle_id' => $puzzle->id, 'name' => 'Buku']);
    }

    public function test_student_cannot_access_erd_management(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'student']))->get(route('admin.erd-puzzles.index'))->assertForbidden();
    }
}
