<?php

namespace Tests\Feature;

use App\Models\Badge;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBadgeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_badge(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $payload = ['name' => 'Query Master', 'description' => 'Selesaikan challenge.', 'criteria_type' => 'xp', 'criteria_value' => 100, 'icon' => '★', 'is_active' => 1];

        $this->actingAs($admin)->post(route('admin.badges.store'), $payload)->assertRedirect();
        $badge = Badge::firstOrFail();
        $this->actingAs($admin)->patch(route('admin.badges.update', $badge), [...$payload, 'name' => 'Query Master Edit'])->assertRedirect();
        $this->assertDatabaseHas('badges', ['id' => $badge->id, 'name' => 'Query Master Edit']);
        $this->actingAs($admin)->delete(route('admin.badges.destroy', $badge))->assertRedirect();
        $this->assertDatabaseMissing('badges', ['id' => $badge->id]);
    }

    public function test_student_cannot_access_badge_management(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'student']))->get(route('admin.badges.index'))->assertForbidden();
    }
}
