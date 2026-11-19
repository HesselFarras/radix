# Radix — Blueprint Implementasi

Sumber aturan: `PRD.md`, HTA/WCAG/Avatar report, dan mockup UI.

## 1. Keputusan produk

- Framework: Laravel + Blade + JavaScript native.
- Auth: email/username + password; tidak ada SSO asli.
- Role: `student`, `admin`.
- Admin dibuat lewat seeder; register publik selalu membuat student.
- Course awal: Basis Data.
- Modul berurutan; modul berikutnya terkunci sampai modul aktif selesai.
- Total modul mockup: 4.
- Dark mode: ditunda; mode terang menjadi default.
- Konten materi: JSON blocks.
- SQL Lab: engine belum dikunci; kontrak data harus engine-agnostic sampai tim memutuskan.
- SQL eksekusi: browser/local; data query tidak dikirim ke server untuk eksekusi.
- PWA: manifest, service worker, IndexedDB, offline cache.
- Back: satu layar sesuai history; Lab/kuis aktif meminta konfirmasi.

## 2. Route map

### Public/auth

| Method | URI | Name | Access |
|---|---|---|---|
| GET | `/` | `home` | public |
| GET | `/login` | `login` | guest |
| POST | `/login` | `login.store` | guest |
| GET | `/register` | `register` | guest |
| POST | `/register` | `register.store` | guest |
| POST | `/logout` | `logout` | auth |
| GET | `/forgot-password` | `password.request` | guest |
| POST | `/forgot-password` | `password.email` | guest |
| GET | `/reset-password/{token}` | `password.reset` | guest |
| POST | `/reset-password` | `password.update` | guest |

### Student

| Method | URI | Name |
|---|---|---|
| GET | `/dashboard` | `dashboard` |
| GET | `/onboarding` | `onboarding.show` |
| POST | `/onboarding/complete` | `onboarding.complete` |
| GET | `/courses/{course}/modules/{module}` | `modules.show` |
| GET | `/topics/{topic}` | `topics.show` |
| POST | `/topics/{topic}/complete` | `topics.complete` |
| POST | `/topics/{topic}/annotations` | `annotations.store` |
| DELETE | `/annotations/{annotation}` | `annotations.destroy` |
| GET | `/quizzes/{quiz}` | `quizzes.show` |
| POST | `/quizzes/{quiz}/attempts` | `quiz-attempts.store` |
| GET | `/quiz-attempts/{attempt}` | `quiz-attempts.show` |
| GET | `/sql-lab` | `sql-lab.index` |
| GET | `/sql-lab/{lab}` | `sql-lab.show` |
| POST | `/sql-lab/{lab}/attempts` | `sql-lab-attempts.store` |
| GET | `/erd-puzzle` | `erd-puzzle.index` |
| GET | `/erd-puzzle/{puzzle}` | `erd-puzzle.show` |
| POST | `/erd-puzzle/{puzzle}/attempts` | `erd-puzzle-attempts.store` |
| GET | `/leaderboard` | `leaderboard.index` |
| GET | `/profile` | `profile.show` |
| GET | `/settings` | `settings.edit` |
| PATCH | `/settings` | `settings.update` |
| GET | `/certificate` | `certificate.show` |
| GET | `/certificate/download` | `certificate.download` |
| POST | `/sync` | `sync.store` |

### Admin

Semua route admin memakai middleware `auth` + `admin`.

```text
/admin                         Admin dashboard
/admin/users                   User management
/admin/courses                Course CRUD
/admin/modules                Module CRUD
/admin/topics                 Topic CRUD + JSON preview
/admin/quizzes                Quiz CRUD
/admin/questions              Question CRUD
/admin/sql-labs               SQL Lab CRUD
/admin/sql-labs/{lab}/steps   Lab step CRUD
/admin/erd-puzzles            ERD Puzzle CRUD
/admin/badges                 Badge CRUD
```

## 3. Authorization

| Aksi | Student | Admin |
|---|---:|---:|
| Baca materi published | Ya | Ya |
| Simpan progress sendiri | Ya | Ya |
| Kerjakan kuis/Lab/puzzle | Ya | Ya |
| Lihat leaderboard | Ya, jika tidak disembunyikan | Ya |
| Kelola konten | Tidak | Ya |
| Kelola user | Tidak | Ya |
| Edit XP manual | Tidak | Tidak |
| Reset progress terbatas | Tidak | Ya |
| Nonaktifkan akun | Tidak | Ya |

User hanya boleh membaca dan mengubah data miliknya sendiri. Admin tetap tidak boleh mengubah skor/XP bebas.

## 4. Database schema

### Core

```text
users
- id, name, nim, email, password, role
- total_xp, streak_days, is_active, is_leaderboard_visible
- onboarding_completed_at, created_at, updated_at

courses
- id, title, slug, description, is_published, created_at, updated_at

enrollments
- id, user_id, course_id, enrolled_at, created_at, updated_at
- unique(user_id, course_id)

modules
- id, course_id, title, slug, description, sort_order, is_published
- estimated_minutes, created_at, updated_at

topics
- id, module_id, title, slug, content_json, sort_order, is_published
- estimated_minutes, created_at, updated_at

topic_progress
- id, user_id, topic_id, completed_at, last_read_at, created_at, updated_at
- unique(user_id, topic_id)

annotations
- id, user_id, topic_id, type, selected_text, note, position_json
- created_at, updated_at
```

`annotations.type`: `highlight`, `bookmark`.

### Check Your Understanding

```text
understandings
- id, topic_id, title, passing_score, created_at, updated_at

understanding_questions
- id, understanding_id, type, question, explanation, points, sort_order
- created_at, updated_at

understanding_options
- id, question_id, option_text, is_correct, sort_order
- created_at, updated_at

understanding_attempts
- id, user_id, understanding_id, score, passed, answers_json, completed_at
- created_at, updated_at
```

### Kuis

```text
quizzes
- id, module_id, title, passing_score, max_xp, is_published
- created_at, updated_at

questions
- id, quiz_id, type, question, explanation, points, sort_order
- created_at, updated_at

question_options
- id, question_id, option_text, is_correct, sort_order
- created_at, updated_at

quiz_attempts
- id, user_id, quiz_id, score, passed, xp_earned, answers_json, completed_at
- created_at, updated_at
```

`questions.type`: `multiple_choice`, `short_answer`.

### SQL Lab

```text
labs
- id, module_id, title, description, mode, schema_json, is_published
- sort_order, created_at, updated_at

lab_steps
- id, lab_id, title, instruction, expected_result_json
- reference_query, xp_reward, sort_order, created_at, updated_at

lab_attempts
- id, user_id, lab_step_id, query_text, result_json, is_correct
- xp_earned, attempted_at, created_at, updated_at
```

`labs.mode`: `practice`, `challenge`.

`schema_json` berisi engine-neutral table definitions dan seed rows. `expected_result_json` menjadi validasi utama; `reference_query` hanya contoh/petunjuk sampai engine final dipilih.

### ERD Puzzle

```text
erd_puzzles
- id, module_id, title, case_text, instructions, max_score, xp_reward
- is_published, created_at, updated_at

erd_components
- id, erd_puzzle_id, type, name, definition_json, sort_order
- created_at, updated_at

erd_relations
- id, erd_puzzle_id, name, definition_json, created_at, updated_at

erd_attempts
- id, user_id, erd_puzzle_id, answer_json, score, passed
- xp_earned, attempt_number, completed_at, created_at, updated_at
```

`erd_components.type`: `entity`, `attribute`.

### Gamification

```text
xp_transactions
- id, user_id, source_type, source_id, amount, idempotency_key
- description, created_at
- unique(user_id, idempotency_key)

badges
- id, name, description, criteria_type, criteria_value, icon, is_active
- created_at, updated_at

user_badges
- id, user_id, badge_id, awarded_at, created_at
- unique(user_id, badge_id)

user_settings
- id, user_id, font_scale, locale, theme, created_at, updated_at
- unique(user_id)

sync_operations
- id, user_id, operation_uuid, operation_type, payload_json, synced_at
- created_at
- unique(user_id, operation_uuid)
```

## 5. JSON block materi

```json
{
  "version": 1,
  "blocks": [
    {"type": "paragraph", "text": "..."},
    {"type": "heading", "level": 2, "text": "..."},
    {"type": "code", "language": "sql", "code": "SELECT ...;"},
    {"type": "highlight", "text": "..."},
    {"type": "comparison", "title": "Jenis JOIN", "items": []},
    {"type": "tip", "title": "Tips", "text": "..."},
    {"type": "image", "src": "...", "alt": "..."}
  ]
}
```

Renderer hanya mengizinkan block type terdaftar. Jangan render HTML mentah dari admin.

## 6. Progress dan unlock

- Topic selesai saat user menekan `Tandai selesai`.
- Module selesai saat semua topic module selesai dan kuis module lulus.
- Course selesai saat semua module selesai.
- Module pertama terbuka secara default.
- Module berikutnya terbuka setelah module sebelumnya selesai.
- Sertifikat tersedia saat semua module course selesai.
- Progress sertifikat: `module_selesai / total_module * 100`.
- Kuis gagal tidak menghapus progress topic; user diarahkan membaca ulang sebelum retry.

## 7. XP awal

Nilai final masih dapat disesuaikan tim. Default seed:

```text
Quiz: skor proporsional, maksimum 100 XP per module
SQL Challenge: 20 XP per step benar, satu kali per step
ERD Puzzle: score proporsional terhadap max_score
Module completion: tidak memberi XP tambahan
```

Semua pemberian XP lewat `xp_transactions`. `idempotency_key` mencegah klaim ganda.

## 8. Badge awal

```text
Langkah Pertama: module pertama selesai
Sekali Lulus: quiz lulus pada attempt pertama
Query Master: semua SQL Challenge satu module selesai
ERD Sempurna: puzzle selesai tanpa kesalahan
Konsisten 7 Hari: belajar tujuh hari berturut-turut
Setengah Jalan: dua dari empat module selesai
Tiga Besar: masuk tiga besar leaderboard
Lulusan Radix: semua module selesai
```

Evaluator badge harus idempotent dan berjalan setelah event progress/quiz/lab/puzzle/XP.

## 9. Migration order

```text
1. users + user_settings
2. courses
3. enrollments
4. modules
5. topics
6. topic_progress
7. annotations
8. understandings + questions/options/attempts
9. quizzes + questions/options/attempts
10. labs + lab_steps + lab_attempts
11. erd_puzzles + components + relations + attempts
12. xp_transactions
13. badges + user_badges
14. sync_operations
```

## 10. Seed demo

- 1 course: Basis Data
- 4 modules sesuai mockup
- 13 topics contoh
- JSON blocks untuk materi Modul 1 dan 2
- Check Your Understanding pada topik utama
- 1 quiz per module, 10 soal, passing grade 70
- 1 Practice Lab dan 1 Challenge Lab
- 1 ERD Puzzle Perpustakaan Kampus
- 8 badges
- 1 admin
- 1 student demo

## 11. Milestone coding

```text
M1 Blueprint dan project Laravel
M2 Design system, responsive shell, auth, role
M3 Course, module, topic, Reader, progress
M4 Quiz, score, retry, XP
M5 SQL Lab dengan adapter engine sementara
M6 ERD Puzzle
M7 Badge, leaderboard, profile, certificate
M8 Admin CRUD
M9 PWA cache, IndexedDB, offline queue
M10 Radi, settings, accessibility audit
M11 Feature/unit/manual tests
M12 Deployment dan laporan
```

## 12. Keputusan tertunda

- Engine SQL final: konsultasikan dengan tim; PostgreSQL pada mockup belum otomatis kompatibel dengan SQL.js/SQLite.
- Dark mode: tetap ditunda.
- SSO: tidak dibuat; autentikasi Laravel biasa.
- Konten 16 pertemuan RPS: masuk setelah mapping RPS final.
- Sertifikat: halaman/download awal dulu; PDF formal dapat ditambahkan setelah flow selesai.

## 13. Definition of Done per fitur

- Match mockup desktop dan mobile.
- Route terlindungi sesuai role.
- Validasi input tersedia.
- State loading, empty, error, success tersedia.
- Keyboard/focus dan label form tersedia.
- Data tidak dobel saat retry/sync.
- Feature test utama lulus.
- Tampilan tidak memakai warna sebagai satu-satunya status.
- Back mengikuti history dan meminta konfirmasi saat progres aktif.

## 14. Urutan implementasi berikutnya

1. Inisialisasi Laravel.
2. Pasang auth dan styling yang sudah dipakai project.
3. Buat migration core: users, courses, enrollments, modules, topics, topic_progress.
4. Buat layout responsive dan auth screens sesuai mockup.
5. Buat seeder course/module/topic.
6. Implement Dashboard dan Module Reader JSON.
7. Jalankan test alur login → dashboard → reader → progress.
8. Baru lanjut quiz.

SQL engine tetap ditahan sampai keputusan tim.

