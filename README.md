# Radix

Platform pembelajaran interaktif untuk materi basis data, SQL, model data, ERD, normalisasi, dan keamanan database.

## Fitur

### Learner

- Dashboard progres, XP, streak, modul aktif, dan topik berikutnya.
- Module Reader dengan syllabus, materi, code block, catatan, highlight, dan bookmark.
- Quiz modul serta Check Your Understanding.
- SQL Lab berbasis SQL.js di browser:
  - Query akademik.
  - Fungsi SQL.
  - DDL/DML toko komputer.
  - JOIN nilai mahasiswa.
  - Audit saldo.
  - Schema dan sample data per lab.
  - Draft `localStorage`, tombol Run, Submit, Copy, keyword SQL, progress, dan riwayat attempt.
- ERD Puzzle interaktif:
  - Cat Store.
  - Rental Mobil.
  - Akademik.
  - Entity, atribut, relation, dan cardinality.
  - Scoring, feedback, XP, dan riwayat attempt.
- Leaderboard, profile, settings, certificate, onboarding, dan authentication.

### Admin

- Dashboard admin.
- Content/topic management.
- SQL Lab dan step CRUD.
- ERD Puzzle, component, dan relation management.
- Quiz dan badge management.
- User activation, leaderboard visibility, dan reset progress per modul.
- Audit log.

## Tech stack

- PHP `^8.3`
- Laravel `^13.17`
- MySQL
- Laravel Blade
- Tailwind CSS `^4`
- Vite `^8`
- SQL.js `^1.17`
- PHPUnit `^12`

## Persyaratan

- PHP 8.3+
- Composer
- Node.js dan npm
- MySQL 8+
- Git

## Instalasi

Clone repository, lalu masuk ke folder project:

```bash
git clone <repository-url> radix
cd radix
```

Install dependency PHP:

```bash
composer install
```

Buat file environment:

```bash
copy .env.example .env
```

Linux/macOS:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

Atur koneksi database di `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=radix
DB_USERNAME=root
DB_PASSWORD=
```

Buat database `radix`, lalu jalankan migration dan seed:

```bash
php artisan migrate --force
php artisan db:seed --force
```

Install dan build frontend:

```bash
npm install
npm run build
```

Jalankan aplikasi:

```bash
php artisan serve
```

Buka `http://127.0.0.1:8000`.

## Development

Jalankan Vite dalam mode watch:

```bash
npm run dev
```

Jalankan server Laravel:

```bash
php artisan serve
```

Atau gunakan command development Composer jika environment mendukung:

```bash
composer run dev
```

## Seed data

Seeder membuat course basis data, modul, topic, quiz, SQL Lab, ERD Puzzle, badge, dan akun development.

```bash
php artisan db:seed --force
```

Seeder menggunakan `updateOrCreate` untuk konten utama. Hindari menjalankan reset progress secara manual kecuali memang diperlukan karena data pengerjaan learner bersifat persisten.

Cek kesehatan konten pembelajaran:

```bash
php artisan radix:report-learning-content
```

Output melaporkan jumlah SQL Lab, step, ERD Puzzle, component, dan relation.

## Akun development

Seeder menyediakan akun administrator:

```text
Email: admin@radix.test
Password: password
```

Gunakan password development saja. Ganti password sebelum deployment.

Akun student dapat dibuat melalui halaman register.

## Testing

Jalankan seluruh test:

```bash
php artisan test --compact
```

Atau:

```bash
composer test
```

Test mencakup authentication, onboarding, module progress, quiz, SQL Lab, ERD Puzzle, leaderboard, settings, admin authorization, dan security headers.

## Production check

Urutan yang disarankan:

```bash
php artisan optimize:clear
php artisan migrate --force
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Jalankan test sebelum cache production:

```bash
php artisan optimize:clear
php artisan test --compact
```

`config:cache`, `route:cache`, dan `view:cache` dijalankan setelah test selesai agar environment testing tidak memakai konfigurasi production cache.

## Struktur utama

```text
app/
  Http/Controllers/       Controller learner dan admin
  Models/                  Model Eloquent
  Services/                Service domain
  Providers/               Service provider

database/
  migrations/              Struktur database
  seeders/                 Konten course dan data development

resources/
  views/                   Blade learner dan admin
  js/                      Entry point frontend
  css/                     Tailwind CSS

routes/
  web.php                  Route learner dan admin
  console.php              Artisan command

tests/Feature/             Regression test aplikasi
```

## Route utama

### Learner

- `/dashboard`
- `/modules/{module}`
- `/topics/{topic}`
- `/sql-lab`
- `/erd-puzzle`
- `/leaderboard`
- `/profile`
- `/settings`
- `/certificate`

### Admin

- `/admin`
- `/admin/content`
- `/admin/labs`
- `/admin/erd-puzzles`
- `/admin/quizzes`
- `/admin/badges`
- `/admin/users`
- `/admin/audit-logs`

Semua route admin dilindungi middleware `auth` dan `admin`.

## Data dan progress

Progress learner disimpan secara dinamis, bukan hardcode:

- Topic completion.
- Understanding attempts.
- Quiz attempts.
- SQL Lab attempts.
- ERD attempts.
- XP transactions.
- Badge ownership.
- Dashboard streak berdasarkan tanggal aktivitas.

SQL Lab memproses query di browser melalui SQL.js. Query dan hasil submit tetap divalidasi server-side untuk scoring dan XP.

## Catatan pengembangan

- Jangan menggunakan `Lab::query()->delete()` atau penghapusan global saat seed.
- Konten lab dan puzzle memakai unique key per module dan title.
- Jangan menghapus attempt learner ketika memperbarui konten.
- ERD submission hanya menerima component dan relation milik puzzle aktif.
- XP memakai idempotency key agar tidak diberikan dua kali untuk step/puzzle yang sama.
- Reset progress harus dilakukan melalui alur admin yang terkontrol.

## Troubleshooting

### SQL Lab tidak muncul

Jalankan:

```bash
php artisan db:seed --force
php artisan radix:report-learning-content
```

Pastikan lab memiliki `is_published = 1`.

### Asset frontend tidak ditemukan

```bash
npm install
npm run build
```

### Test mengembalikan HTTP 419

Bersihkan cache sebelum test:

```bash
php artisan optimize:clear
php artisan test --compact
```

### Migration unique gagal

Periksa duplicate data pada `labs`, `erd_puzzles`, `erd_components`, dan `erd_relations` sebelum menjalankan migration production.

## License

Project ini menggunakan MIT License, mengikuti konfigurasi Laravel project.
