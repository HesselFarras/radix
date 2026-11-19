<?php

namespace Tests\Feature;

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
        $this->actingAs($admin)->patch(route('admin.users.active', $student))->assertRedirect();
        $this->actingAs($admin)->patch(route('admin.users.leaderboard', $student))->assertRedirect();
        $this->assertFalse($student->fresh()->is_active);
        $this->assertFalse($student->fresh()->is_leaderboard_visible);
    }

    public function test_student_cannot_access_user_management(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'student']))->get(route('admin.users.index'))->assertForbidden();
    }
}
