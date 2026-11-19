<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_register_and_reach_dashboard(): void
    {
        $response = $this->post('/register', [
            'name' => 'Dika Pratama',
            'nim' => '2207411038',
            'email' => 'dika@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/onboarding');
        $this->assertAuthenticatedAs(User::where('email', 'dika@example.com')->first());
    }

    public function test_admin_route_rejects_student(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'student']));

        $this->get('/admin')->assertForbidden();
    }
}
