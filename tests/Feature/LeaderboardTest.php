<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaderboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_leaderboard_orders_visible_users_and_hides_opted_out_users(): void
    {
        $current = User::factory()->create(['name' => 'Current', 'total_xp' => 100]);
        User::factory()->create(['name' => 'Top', 'total_xp' => 200]);
        User::factory()->create(['name' => 'Hidden', 'total_xp' => 999, 'is_leaderboard_visible' => false]);

        $this->actingAs($current)->get(route('leaderboard.index'))
            ->assertOk()
            ->assertSeeInOrder(['Top', 'Current'])
            ->assertDontSee('Hidden')
            ->assertSee('#2');
    }
}
