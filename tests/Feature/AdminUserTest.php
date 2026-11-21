<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_search_and_toggle_student_controls(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['name' => 'Dika Search', 'role' => 'student']);

        $this->actingAs($admin)->get(route('admin.users.index', ['search' => 'Dika']))->assertOk()->assertSee('Dika Search');
        $this->actingAs($admin)->patch(route('admin.users.active', $student), ['reason' => 'Pelanggaran aturan'])->assertRedirect();
        $this->actingAs($admin)->patch(route('admin.users.leaderboard', $student), ['reason' => 'Privasi pengguna'])->assertRedirect();
        $this->assertDatabaseCount('admin_action_logs', 2);

        $this->actingAs($admin)->delete(route('admin.users.progress.reset', [$student, $module = Module::create(['course_id' => Course::create(['title' => 'C', 'slug' => 'c'])->id, 'title' => 'M', 'slug' => 'm', 'sort_order' => 1])]), ['reason' => 'Reset progress test'])->assertRedirect();
        $this->assertDatabaseHas('admin_action_logs', ['action' => 'reset_module_progress']);
        $this->assertFalse($student->fresh()->is_active);
        $this->assertFalse($student->fresh()->is_leaderboard_visible);
    }

    public function test_student_cannot_access_user_management(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'student']))->get(route('admin.users.index'))->assertForbidden();
    }
}
