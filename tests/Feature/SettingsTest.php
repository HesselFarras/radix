<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_settings_and_hide_from_leaderboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->patch(route('settings.update'), [
            'font_scale' => 150,
            'locale' => 'id',
            'theme' => 'light',
        ])->assertRedirect(route('settings.edit'));

        $this->assertFalse($user->fresh()->is_leaderboard_visible);
        $this->assertDatabaseHas('user_settings', ['user_id' => $user->id, 'font_scale' => 150, 'locale' => 'id', 'theme' => 'light']);
    }
}
