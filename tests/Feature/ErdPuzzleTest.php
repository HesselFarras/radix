<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\ErdComponent;
use App\Models\ErdPuzzle;
use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErdPuzzleTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_open_published_erd_puzzle(): void
    {
        $user = User::factory()->create();
        $course = Course::create(['title' => 'Course', 'slug' => 'course', 'is_published' => true]);
        $module = Module::create(['course_id' => $course->id, 'title' => 'Module', 'slug' => 'module', 'sort_order' => 1, 'is_published' => true]);
        $puzzle = ErdPuzzle::create(['module_id' => $module->id, 'title' => 'Perpustakaan', 'case_text' => 'Susun ERD perpustakaan.', 'is_published' => true, 'xp_reward' => 25]);
        $component = ErdComponent::create(['erd_puzzle_id' => $puzzle->id, 'type' => 'entity', 'name' => 'Buku', 'sort_order' => 1]);

        $this->actingAs($user)->get(route('erd-puzzle.index'))->assertOk()->assertSee($puzzle->title);
        $this->actingAs($user)->get(route('erd-puzzle.show', $puzzle))->assertOk()->assertSee('Canvas ERD')->assertSee('Tempatkan ke slot terpilih');
        $this->actingAs($user)->post(route('erd-puzzle.attempts.store', $puzzle), ['components' => [$component->id]])->assertRedirect();
        $this->assertDatabaseHas('erd_attempts', ['user_id' => $user->id, 'erd_puzzle_id' => $puzzle->id, 'score' => 100, 'passed' => true, 'xp_earned' => 25]);
        $this->assertDatabaseHas('xp_transactions', ['user_id' => $user->id, 'source_type' => 'erd_puzzle', 'amount' => 25]);
    }
}
