<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\Course;
use App\Models\Module;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Quiz;
use App\Models\Topic;
use App\Models\Understanding;
use App\Models\UnderstandingOption;
use App\Models\UnderstandingQuestion;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@radix.test'],
            [
                'name' => 'Admin Radix',
                'nim' => 'ADMIN-001',
                'password' => 'password',
                'role' => 'admin',
            ],
        );

        $student = User::updateOrCreate(
            ['email' => 'dika@radix.test'],
            [
                'name' => 'Dika Pratama',
                'nim' => '2207411038',
                'password' => 'password',
                'role' => 'student',
            ],
        );

        $course = Course::updateOrCreate(
            ['slug' => 'basis-data'],
            [
                'title' => 'Basis Data',
                'description' => 'Belajar konsep database, SQL, keamanan, dan desain database.',
                'is_published' => true,
            ],
        );

        $moduleData = [
            ['title' => 'Konsep Dasar Database', 'slug' => 'konsep-dasar-database', 'description' => 'Fondasi database relasional.', 'estimated_minutes' => 30, 'topics' => ['Data dan Informasi', 'Database Relasional']],
            ['title' => 'SQL dan Query', 'slug' => 'sql-dan-query', 'description' => 'Latihan query SQL bertahap.', 'estimated_minutes' => 45, 'topics' => ['DDL, DML, dan DCL', 'Query', 'Fungsi SQL', 'Teknik JOIN']],
            ['title' => 'Keamanan Database', 'slug' => 'keamanan-database', 'description' => 'Menjaga database tetap aman.', 'estimated_minutes' => 35, 'topics' => ['Autentikasi Database', 'Hak Akses', 'Backup dan Recovery']],
            ['title' => 'Desain Database', 'slug' => 'desain-database', 'description' => 'Merancang database yang rapi dan konsisten.', 'estimated_minutes' => 45, 'topics' => ['ERD', 'Normalisasi', 'Relasi Antar Entitas', 'Implementasi Skema']],
        ];

        foreach ($moduleData as $index => $data) {
            $topics = $data['topics'];
            unset($data['topics']);
            $module = Module::updateOrCreate(
                ['course_id' => $course->id, 'slug' => $data['slug']],
                [...$data, 'course_id' => $course->id, 'sort_order' => $index + 1, 'is_published' => true],
            );

            foreach ($topics as $topicIndex => $title) {
                $topic = Topic::updateOrCreate(
                    ['module_id' => $module->id, 'slug' => 'topik-'.($topicIndex + 1)],
                    [
                        'title' => $title,
                        'module_id' => $module->id,
                        'content_json' => ['version' => 1, 'blocks' => [
                            ['type' => 'paragraph', 'text' => "Materi $title pada Radix."],
                            ['type' => 'tip', 'title' => 'Tips Praktik Mahasiswa', 'text' => 'Catat konsep penting dan coba contoh secara bertahap.'],
                        ]],
                        'sort_order' => $topicIndex + 1,
                        'estimated_minutes' => 6,
                        'is_published' => true,
                    ],
                );

                if ($topicIndex === 0) {
                    $understanding = Understanding::updateOrCreate(
                        ['topic_id' => $topic->id],
                        ['title' => 'Check Your Understanding', 'passing_score' => 70],
                    );
                    $question = UnderstandingQuestion::updateOrCreate(
                        ['understanding_id' => $understanding->id, 'sort_order' => 1],
                        ['type' => 'multiple_choice', 'question' => "Pilih pernyataan yang benar tentang $title.", 'explanation' => 'Jawaban benar menunjukkan pemahaman konsep utama.', 'points' => 1],
                    );
                    UnderstandingOption::updateOrCreate(['understanding_question_id' => $question->id, 'sort_order' => 1], ['option_text' => 'Konsep utama sudah dipahami.', 'is_correct' => true]);
                    UnderstandingOption::updateOrCreate(['understanding_question_id' => $question->id, 'sort_order' => 2], ['option_text' => 'Konsep ini tidak perlu dipelajari.', 'is_correct' => false]);
                }
            }
        }

        foreach ($course->modules as $module) {
            $quiz = Quiz::updateOrCreate(
                ['module_id' => $module->id],
                ['title' => 'Kuis '.$module->title, 'passing_score' => 70, 'max_xp' => 100, 'is_published' => true],
            );
            for ($questionIndex = 1; $questionIndex <= 10; $questionIndex++) {
                $question = Question::updateOrCreate(
                    ['quiz_id' => $quiz->id, 'sort_order' => $questionIndex],
                    ['type' => 'multiple_choice', 'question' => "Soal $questionIndex tentang {$module->title}.", 'explanation' => 'Pelajari kembali konsep utama pada materi modul.', 'points' => 1],
                );
                QuestionOption::updateOrCreate(['question_id' => $question->id, 'sort_order' => 1], ['option_text' => 'Pernyataan yang benar.', 'is_correct' => true]);
                QuestionOption::updateOrCreate(['question_id' => $question->id, 'sort_order' => 2], ['option_text' => 'Pernyataan yang kurang tepat.', 'is_correct' => false]);
            }
        }

        $student->enrollments()->firstOrCreate(['course_id' => $course->id]);

        foreach ([
            ['name' => 'Langkah Pertama', 'description' => 'Menyelesaikan modul pertama.', 'criteria_type' => 'first_module', 'icon' => '🌱'],
            ['name' => 'Setengah Jalan', 'description' => 'Menyelesaikan dua modul.', 'criteria_type' => 'half_course', 'icon' => '🌿'],
            ['name' => 'Lulusan Radix', 'description' => 'Menyelesaikan semua modul.', 'criteria_type' => 'course_complete', 'icon' => '🏆'],
            ['name' => 'Sekali Lulus', 'description' => 'Lulus kuis.', 'criteria_type' => 'first_quiz_pass', 'icon' => '✅'],
            ['name' => 'ERD Sempurna', 'description' => 'Menyelesaikan puzzle ERD dengan skor 100.', 'criteria_type' => 'perfect_erd', 'icon' => '💎'],
        ] as $badge) {
            Badge::updateOrCreate(['name' => $badge['name']], [...$badge, 'is_active' => true]);
        }
    }
}
