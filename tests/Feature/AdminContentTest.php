<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Module;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_topic(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $course = Course::create(['title' => 'Course', 'slug' => 'course', 'is_published' => true]);
        $module = Module::create(['course_id' => $course->id, 'title' => 'Module', 'slug' => 'module', 'sort_order' => 1, 'is_published' => true]);
        $payload = ['title' => 'Materi Baru', 'slug' => 'materi-baru', 'content_json' => '{"version":1,"blocks":[]}', 'sort_order' => 1, 'estimated_minutes' => 10, 'is_published' => 1];

        $this->actingAs($admin)->post(route('admin.content.topics.store', $module), $payload)->assertRedirect();
        $topic = Topic::firstOrFail();
        $this->assertSame('Materi Baru', $topic->title);
        $this->actingAs($admin)->patch(route('admin.content.topics.update', $topic), [...$payload, 'title' => 'Materi Edit'])->assertRedirect();
        $this->assertDatabaseHas('topics', ['id' => $topic->id, 'title' => 'Materi Edit']);
        $this->actingAs($admin)->delete(route('admin.content.topics.destroy', $topic))->assertRedirect();
        $this->assertDatabaseMissing('topics', ['id' => $topic->id]);
    }

    public function test_student_cannot_access_content_management(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'student']))
            ->get(route('admin.content.index'))
            ->assertForbidden();
    }
}
