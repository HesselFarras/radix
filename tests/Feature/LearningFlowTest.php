<?php

namespace Tests\Feature;

use App\Models\Annotation;
use App\Models\Course;
use App\Models\Module;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LearningFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_open_module_topic_and_complete_it(): void
    {
        $user = User::factory()->create();
        $course = Course::create([
            'title' => 'Basis Data',
            'slug' => 'basis-data',
            'is_published' => true,
        ]);
        $module = Module::create([
            'course_id' => $course->id,
            'title' => 'Konsep Dasar Database',
            'slug' => 'konsep-dasar-database',
            'sort_order' => 1,
            'is_published' => true,
        ]);
        $topic = Topic::create([
            'module_id' => $module->id,
            'title' => 'Data dan Informasi',
            'slug' => 'data-dan-informasi',
            'content_json' => ['version' => 1, 'blocks' => [['type' => 'paragraph', 'text' => 'Materi']]],
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $this->actingAs($user);
        $this->get(route('modules.show', $module))->assertOk()->assertSee($topic->title);
        $this->get(route('topics.show', $topic))->assertOk()->assertSee('Materi');
        $this->post(route('topics.complete', $topic))->assertRedirect(route('topics.show', $topic));

        $this->assertDatabaseHas('topic_progress', [
            'user_id' => $user->id,
            'topic_id' => $topic->id,
        ]);

        $this->post(route('annotations.store', $topic), [
            'type' => 'highlight',
            'selected_text' => 'Materi penting',
            'note' => 'Baca lagi sebelum kuis.',
        ])->assertRedirect(route('topics.show', $topic));

        $annotation = Annotation::query()->firstOrFail();
        $this->assertSame($user->id, $annotation->user_id);
        $this->delete(route('annotations.destroy', $annotation))->assertRedirect(route('topics.show', $topic));
        $this->assertDatabaseMissing('annotations', ['id' => $annotation->id]);
    }
}
