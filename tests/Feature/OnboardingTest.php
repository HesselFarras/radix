<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OnboardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_pwa_manifest_and_service_worker_are_public(): void
    {
        $this->get('/manifest.webmanifest')->assertOk()->assertHeader('content-type', 'application/manifest+json');
        $this->get('/service-worker.js')->assertOk()->assertSee('radix-shell-v1');
    }

    public function test_user_can_complete_onboarding_and_repeat_it(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('onboarding.show', ['step' => 3]))->assertOk()->assertSee('Langkah 3 dari 5')->assertSee('Lab SQL');
        $this->actingAs($user)->post(route('onboarding.complete'))->assertRedirect(route('dashboard'));
        $this->assertNotNull($user->fresh()->onboarding_completed_at);
        $this->actingAs($user)->get(route('onboarding.show'))->assertOk();
    }

    public function test_each_tour_step_shows_its_own_preview_and_navigation(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('onboarding.show'))
            ->assertOk()
            ->assertSee('Peta modul')
            ->assertSee('Lewati tur')
            ->assertDontSee('← Kembali', false);

        $this->actingAs($user)->get(route('onboarding.show', ['step' => 2]))
            ->assertOk()
            ->assertSee('Module Reader')
            ->assertSee('← Kembali', false)
            ->assertSee('aria-current="step"', false);

        $this->actingAs($user)->get(route('onboarding.show', ['step' => 5]))
            ->assertOk()
            ->assertSee('Langkah 5 dari 5')
            ->assertSee('Mulai belajar')
            ->assertDontSee('Lanjut →');
    }

    public function test_out_of_range_step_is_clamped(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('onboarding.show', ['step' => 99]))->assertOk()->assertSee('Langkah 5 dari 5');
        $this->actingAs($user)->get(route('onboarding.show', ['step' => -3]))->assertOk()->assertSee('Langkah 1 dari 5');
    }
}
