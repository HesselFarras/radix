<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_shows_platform_metrics(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('User aktif')
            ->assertSee('Completion rate');
    }

    public function test_student_cannot_open_admin_dashboard(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'student']))
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }
}
