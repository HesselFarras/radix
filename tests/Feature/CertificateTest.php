<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Module;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateTest extends TestCase
{
    use RefreshDatabase;

    public function test_certificate_is_locked_until_all_modules_are_complete(): void
    {
        $user = User::factory()->create();
        $course = Course::create(['title' => 'Basis Data', 'slug' => 'basis-data', 'is_published' => true]);
        $module = Module::create(['course_id' => $course->id, 'title' => 'Module', 'slug' => 'module', 'sort_order' => 1, 'is_published' => true]);
        Topic::create(['module_id' => $module->id, 'title' => 'Topik', 'slug' => 'topik', 'content_json' => ['blocks' => []], 'sort_order' => 1, 'is_published' => true]);

        $this->actingAs($user)->get(route('certificate.show'))->assertForbidden();
    }

    public function test_certificate_is_available_after_all_modules_complete(): void
    {
        $user = User::factory()->create(['nim' => '123']);
        $course = Course::create(['title' => 'Basis Data', 'slug' => 'basis-data', 'is_published' => true]);
        $module = Module::create(['course_id' => $course->id, 'title' => 'Module', 'slug' => 'module', 'sort_order' => 1, 'is_published' => true]);
        $topic = Topic::create(['module_id' => $module->id, 'title' => 'Topik', 'slug' => 'topik', 'content_json' => ['blocks' => []], 'sort_order' => 1, 'is_published' => true]);
        $user->topicProgress()->create(['topic_id' => $topic->id, 'completed_at' => now()]);

        $this->actingAs($user)->get(route('certificate.show'))->assertOk()->assertSee('Sertifikat Kelulusan')->assertSee($user->name);
    }
}
