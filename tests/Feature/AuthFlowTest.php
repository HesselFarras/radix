<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Module;
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

    public function test_active_admin_can_login_and_reach_admin_dashboard(): void
    {
        $admin = User::factory()->create(['email' => 'admin@example.com', 'role' => 'admin', 'is_active' => true, 'onboarding_completed_at' => now()]);

        $this->post('/login', ['email' => $admin->email, 'password' => 'password'])
            ->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($admin);
        $this->get('/admin')->assertOk();
    }

    public function test_active_student_can_login(): void
    {
        $student = User::factory()->create(['email' => 'student@example.com', 'role' => 'student', 'is_active' => true, 'onboarding_completed_at' => now()]);

        $this->post('/login', ['email' => $student->email, 'password' => 'password'])
            ->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($student);
    }

    public function test_student_dashboard_loads_learning_relationships(): void
    {
        $user = User::factory()->create(['onboarding_completed_at' => now()]);
        $course = Course::create(['title' => 'Course', 'slug' => 'basis-data', 'is_published' => true]);
        Module::create(['course_id' => $course->id, 'title' => 'Module', 'slug' => 'module-dashboard', 'sort_order' => 1, 'is_published' => true]);

        $this->actingAs($user)->get('/dashboard')->assertOk();
    }

    public function test_dashboard_handles_missing_published_course(): void
    {
        $user = User::factory()->create(['onboarding_completed_at' => now()]);

        $this->actingAs($user)->get('/dashboard')->assertOk();
    }

    public function test_admin_route_rejects_student(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'student']));

        $this->get('/admin')->assertForbidden();
    }

    public function test_forgot_password_page_is_available(): void
    {
        $this->get('/forgot-password')->assertOk()->assertSee('Lupa Kata Sandi?');
    }

    public function test_inactive_user_cannot_login(): void
    {
        $user = User::factory()->create(['email' => 'inactive@example.com', 'is_active' => false]);

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect()
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_is_rate_limited(): void
    {
        $payload = ['email' => 'unknown@example.com', 'password' => 'wrong-password'];

        foreach (range(1, 5) as $_) {
            $this->post('/login', $payload)->assertRedirect();
        }

        $this->post('/login', $payload)->assertTooManyRequests();
    }
}
