<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\Course;
use App\Models\ErdComponent;
use App\Models\ErdPuzzle;
use App\Models\ErdRelation;
use App\Models\Lab;
use App\Models\LabStep;
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
            ['title' => 'Pendahuluan Basis Data', 'slug' => 'pendahuluan-basis-data', 'description' => 'Tujuan pembelajaran, kasus Cat Store, dan pengantar aplikasi database.', 'estimated_minutes' => 25, 'topics' => ['Pengenalan Basis Data', 'Kasus Cat Store', 'Data dan Informasi']],
            ['title' => 'Konsep Basis Data', 'slug' => 'konsep-basis-data', 'description' => 'Struktur, manfaat, DBMS, abstraksi, dan komponen sistem database.', 'estimated_minutes' => 40, 'topics' => ['Struktur Data', 'Database dan DBMS', 'Abstraksi Data', 'Komponen DBMS']],
            ['title' => 'SQL Dasar', 'slug' => 'sql-dasar', 'description' => 'DDL, DML, DCL, constraint, dan transaksi pada SQL.', 'estimated_minutes' => 45, 'topics' => ['DDL, DML, dan DCL', 'Constraint Data', 'Transaksi Database']],
            ['title' => 'Query SQL', 'slug' => 'query-sql', 'description' => 'Query satu tabel, multi-tabel, pengurutan, dan penyaringan data.', 'estimated_minutes' => 50, 'topics' => ['Query Dasar', 'Query Multi-Tabel', 'ORDER BY dan LIMIT']],
            ['title' => 'Fungsi SQL', 'slug' => 'fungsi-sql', 'description' => 'Fungsi sistem, agregat, string, tanggal, numerik, dan logika.', 'estimated_minutes' => 50, 'topics' => ['Fungsi Sistem dan Agregat', 'Fungsi String', 'Fungsi Tanggal dan Numerik']],
            ['title' => 'Teknik JOIN', 'slug' => 'teknik-join', 'description' => 'Menghubungkan tabel untuk menghasilkan informasi lintas entitas.', 'estimated_minutes' => 45, 'topics' => ['Konsep JOIN', 'JOIN Dua Tabel', 'JOIN Banyak Tabel']],
            ['title' => 'Model Data dan ERD', 'slug' => 'model-data-erd', 'description' => 'Model data konseptual, entity, atribut, relasi, dan ERD.', 'estimated_minutes' => 50, 'topics' => ['Model Data', 'Entity dan Atribut', 'Entity Relationship Diagram']],
            ['title' => 'Normalisasi Database', 'slug' => 'normalisasi-database', 'description' => 'Mengurangi redundansi serta mencegah anomali data.', 'estimated_minutes' => 40, 'topics' => ['Redundansi dan Anomali', 'Normalisasi 1NF dan 2NF', 'Normalisasi 3NF']],
            ['title' => 'Transformasi ERD ke Tabel', 'slug' => 'transformasi-erd', 'description' => 'Mengubah rancangan ERD menjadi tabel relasional dan foreign key.', 'estimated_minutes' => 40, 'topics' => ['Aturan Transformasi', 'Primary dan Foreign Key', 'Implementasi Skema']],
            ['title' => 'Keamanan Database', 'slug' => 'keamanan-database', 'description' => 'Hak akses, pengguna, dan perlindungan database.', 'estimated_minutes' => 35, 'topics' => ['Pengguna Database', 'Hak Akses', 'Keamanan dan Backup']],
        ];

        foreach ($moduleData as $index => $data) {
            $topics = $data['topics'];
            unset($data['topics']);
            $module = Module::query()->firstOrNew(['course_id' => $course->id, 'sort_order' => $index + 1]);
            $module->fill([...$data, 'course_id' => $course->id, 'sort_order' => $index + 1, 'is_published' => true]);
            $module->save();

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

        $openLessons = [
            'Data dan Informasi' => [
                ['type' => 'paragraph', 'text' => 'Data adalah fakta mentah; informasi adalah data yang sudah diolah sehingga membantu pengambilan keputusan. Database menyimpan data secara terstruktur agar mudah dicari, diubah, dan dipelihara.'],
                ['type' => 'heading', 'text' => 'Contoh sederhana'],
                ['type' => 'code', 'code' => "SELECT name, major\nFROM students\nWHERE major = 'Sistem Informasi';"],
                ['type' => 'tip', 'title' => 'Praktik baik', 'text' => 'Gunakan nama kolom yang jelas dan simpan satu fakta di satu tempat.'],
            ],
            'Database Relasional' => [
                ['type' => 'paragraph', 'text' => 'Database relasional menyimpan data dalam tabel. Baris mewakili record, kolom mewakili atribut, primary key mengidentifikasi record secara unik, dan foreign key menghubungkan tabel.'],
                ['type' => 'heading', 'text' => 'Kunci utama'],
                ['type' => 'paragraph', 'text' => 'Primary key harus unik dan tidak NULL. Foreign key menjaga agar hubungan antar tabel tetap valid.'],
            ],
            'DDL, DML, dan DCL' => [
                ['type' => 'paragraph', 'text' => 'DDL mengatur struktur database seperti CREATE TABLE dan ALTER TABLE. DML mengelola data seperti SELECT, INSERT, UPDATE, dan DELETE. DCL mengatur hak akses seperti GRANT dan REVOKE.'],
                ['type' => 'code', 'code' => "CREATE TABLE courses (\n  id INTEGER PRIMARY KEY,\n  title TEXT NOT NULL\n);"],
            ],
            'Query' => [
                ['type' => 'paragraph', 'text' => 'Query mengambil data menggunakan SELECT. WHERE menyaring baris, ORDER BY mengurutkan hasil, dan LIMIT membatasi jumlah hasil.'],
                ['type' => 'code', 'code' => "SELECT id, name\nFROM students\nWHERE id > 1\nORDER BY name;"],
            ],
            'Fungsi SQL' => [
                ['type' => 'paragraph', 'text' => 'Fungsi agregat membantu merangkum data. COUNT menghitung baris, AVG menghitung rata-rata, SUM menjumlahkan nilai, dan GROUP BY membentuk kelompok analisis.'],
                ['type' => 'code', 'code' => "SELECT major, COUNT(*) AS total\nFROM students\nGROUP BY major;"],
            ],
            'Teknik JOIN' => [
                ['type' => 'paragraph', 'text' => 'JOIN menggabungkan baris dari beberapa tabel berdasarkan kondisi yang berhubungan. INNER JOIN hanya mengembalikan pasangan yang cocok; LEFT JOIN tetap mempertahankan semua baris dari tabel kiri.'],
                ['type' => 'code', 'code' => "SELECT students.name, enrollments.course_id\nFROM students\nINNER JOIN enrollments ON enrollments.student_id = students.id;"],
            ],
            'Normalisasi' => [
                ['type' => 'paragraph', 'text' => 'Normalisasi mengurangi duplikasi dan anomali data. Mulai dengan satu nilai per sel (1NF), pastikan atribut bergantung pada seluruh key (2NF), lalu hilangkan ketergantungan transitif (3NF).'],
                ['type' => 'tip', 'title' => 'Checklist', 'text' => 'Cari data berulang, pisahkan entitas, lalu hubungkan tabel dengan foreign key.'],
            ],
        ];
        $materialLessons = [
            'Pengenalan Basis Data' => [['type' => 'paragraph', 'text' => 'Database adalah kumpulan data terorganisir yang disimpan secara elektronik agar mudah diakses, dikelola, dan diperbarui.'], ['type' => 'tip', 'title' => 'Kasus Cat Store', 'text' => 'Database membantu mencegah data pelanggan ganda, alamat yang keliru, dan kesalahan pengiriman.']],
            'Kasus Cat Store' => [['type' => 'paragraph', 'text' => 'Cat Store membutuhkan data Pelanggan, Barang, Pesanan, dan Detail Penjualan. Setiap entitas memiliki ID unik dan transaksi menghubungkan pelanggan dengan barang yang dibeli.'], ['type' => 'code', 'code' => "CREATE TABLE pelanggan (id_pelanggan INT PRIMARY KEY, nama VARCHAR(100), telepon VARCHAR(20), alamat TEXT);"]],
            'Struktur Data' => [['type' => 'paragraph', 'text' => 'Tabel menyimpan data dalam baris dan kolom. Baris mewakili record, sedangkan kolom mendefinisikan atribut seperti nama, harga, tanggal, atau status.']],
            'Database dan DBMS' => [['type' => 'paragraph', 'text' => 'DBMS mengelola penyimpanan, akses, keamanan, dan konsistensi database. Contohnya MySQL, Oracle, dan SQL Server.'], ['type' => 'tip', 'title' => 'Tiga level abstraksi', 'text' => 'Kenali level fisik, logis, dan eksternal untuk memahami cara data disimpan dan digunakan.']],
            'Abstraksi Data' => [['type' => 'paragraph', 'text' => 'Level fisik menjelaskan penyimpanan, level logis menjelaskan tabel dan relasi, sedangkan level eksternal menampilkan subset data untuk kebutuhan pengguna.']],
            'Komponen DBMS' => [['type' => 'paragraph', 'text' => 'Komponen DBMS meliputi hardware, software, data, prosedur, dan people. DBA mengelola sistem, end user menggunakan aplikasi, dan programmer menghubungkan aplikasi dengan database.']],
            'Constraint Data' => [['type' => 'paragraph', 'text' => 'Constraint menjaga integritas data melalui PRIMARY KEY, FOREIGN KEY, NOT NULL, UNIQUE, DEFAULT, dan AUTO_INCREMENT.'], ['type' => 'code', 'code' => "CREATE TABLE produk (id INT PRIMARY KEY AUTO_INCREMENT, nama VARCHAR(100) NOT NULL, harga INT CHECK (harga > 0));"]],
            'Transaksi Database' => [['type' => 'paragraph', 'text' => 'Transaksi mengelompokkan operasi data. COMMIT menyimpan perubahan, sedangkan ROLLBACK membatalkan perubahan yang belum disimpan.']],
            'Query Dasar' => [['type' => 'paragraph', 'text' => 'SELECT memilih kolom, FROM menentukan tabel, dan WHERE menyaring baris berdasarkan kondisi.'], ['type' => 'code', 'code' => "SELECT id_unit, nama_unit\nFROM unit\nWHERE tarif_jam < 15000;"]],
            'Query Multi-Tabel' => [['type' => 'paragraph', 'text' => 'Query multi-tabel membutuhkan relasi antar kolom, biasanya melalui primary key dan foreign key.'], ['type' => 'code', 'code' => "SELECT k.nama_karyawan, u.nama_unit\nFROM karyawan k\nJOIN unit u ON u.id_unit = k.id_unit;"]],
            'ORDER BY dan LIMIT' => [['type' => 'paragraph', 'text' => 'ORDER BY mengurutkan hasil query. LIMIT membatasi jumlah baris yang ditampilkan.']],
            'Fungsi Sistem dan Agregat' => [['type' => 'paragraph', 'text' => 'Fungsi agregat meliputi COUNT, AVG, SUM, MIN, dan MAX. GROUP BY mengelompokkan baris sebelum fungsi agregat dijalankan.'], ['type' => 'code', 'code' => 'SELECT sks, COUNT(*) AS total FROM matakuliah GROUP BY sks;']],
            'Fungsi String' => [['type' => 'paragraph', 'text' => 'Fungsi string mengolah teks, misalnya MID untuk mengambil sebagian karakter dan FORMAT untuk menampilkan angka dengan format tertentu.']],
            'Fungsi Tanggal dan Numerik' => [['type' => 'paragraph', 'text' => 'CURDATE mengambil tanggal hari ini, DATE_FORMAT mengubah tampilan tanggal, dan DATEDIFF menghitung selisih tanggal.']],
            'Konsep JOIN' => [['type' => 'paragraph', 'text' => 'JOIN menggabungkan data dari tabel yang memiliki kolom relasi. INNER JOIN mengambil pasangan yang cocok, LEFT JOIN mempertahankan seluruh baris tabel kiri.']],
            'JOIN Dua Tabel' => [['type' => 'code', 'code' => "SELECT k.nama_karyawan, u.nama_unit\nFROM karyawan k\nINNER JOIN unit u ON u.id_unit = k.id_unit;"]],
            'JOIN Banyak Tabel' => [['type' => 'code', 'code' => "SELECT k.nama_karyawan, u.nama_unit, j.total_jam, u.tarif_jam * j.total_jam AS honor\nFROM karyawan k\nJOIN unit u ON u.id_unit = k.id_unit\nJOIN jam_kerja j ON j.id_karyawan = k.id_karyawan;"]],
            'Model Data' => [['type' => 'paragraph', 'text' => 'Model data menggambarkan objek, atribut, dan hubungan yang dibutuhkan aplikasi sebelum tabel dibuat.']],
            'Entity dan Atribut' => [['type' => 'paragraph', 'text' => 'Entity adalah objek yang datanya disimpan. Atribut menjelaskan karakteristik entity, misalnya Pelanggan memiliki nama, telepon, dan alamat.']],
            'Entity Relationship Diagram' => [['type' => 'paragraph', 'text' => 'ERD menggambarkan entity, atribut, primary key, dan relasi antar entity. ERD Cat Store menghubungkan Pelanggan, Pesanan, Barang, dan Detail Penjualan.']],
            'Redundansi dan Anomali' => [['type' => 'paragraph', 'text' => 'Data berulang dapat menyebabkan anomali insert, update, dan delete. Normalisasi memisahkan fakta ke tabel yang tepat.']],
            'Normalisasi 1NF dan 2NF' => [['type' => 'paragraph', 'text' => '1NF memastikan satu nilai per sel. 2NF memastikan atribut non-key bergantung pada seluruh primary key.']],
            'Normalisasi 3NF' => [['type' => 'paragraph', 'text' => '3NF menghilangkan ketergantungan transitif sehingga atribut non-key hanya bergantung pada primary key.']],
            'Aturan Transformasi' => [['type' => 'paragraph', 'text' => 'Entity menjadi tabel, atribut menjadi kolom, dan relasi diterapkan sebagai foreign key atau tabel penghubung.']],
            'Primary dan Foreign Key' => [['type' => 'paragraph', 'text' => 'Primary key mengidentifikasi record secara unik. Foreign key menghubungkan record ke primary key pada tabel lain.']],
            'Implementasi Skema' => [['type' => 'code', 'code' => "CREATE TABLE detail_penjualan (id_penjualan INT, id_barang INT, jumlah INT NOT NULL, PRIMARY KEY (id_penjualan, id_barang));"]],
            'Pengguna Database' => [['type' => 'paragraph', 'text' => 'DBA mengelola database, end user menggunakan sistem, dan programmer aplikasi mengakses database melalui program.']],
            'Hak Akses' => [['type' => 'paragraph', 'text' => 'Hak akses membatasi operasi user sesuai tanggung jawabnya. GRANT memberikan privilege, REVOKE mencabut privilege.'], ['type' => 'code', 'code' => 'GRANT SELECT ON cat_store.* TO analyst;']],
            'Keamanan dan Backup' => [['type' => 'paragraph', 'text' => 'Keamanan mencakup autentikasi, otorisasi, pembatasan akses, dan backup berkala untuk pemulihan data.']],
        ];
        $openLessons = $openLessons + $materialLessons;
        foreach ($openLessons as $title => $blocks) {
            Topic::query()->where('title', $title)->update(['content_json' => ['version' => 1, 'license' => 'Materi kuliah dari arsip pengguna', 'blocks' => $blocks]]);
        }

        $sqlModule = $course->modules->firstWhere('slug', 'sql-dasar') ?? $course->modules->firstWhere('slug', 'query-sql');
        if ($sqlModule) {
            $lab = Lab::updateOrCreate(
                ['module_id' => $sqlModule->id, 'title' => 'Teknik Query SQL'],
                ['description' => 'Latihan SELECT dan filtering data mahasiswa.', 'mode' => 'challenge', 'sort_order' => 1, 'is_published' => true, 'schema_json' => ['tables' => [['name' => 'students', 'columns' => [['name' => 'id', 'type' => 'INTEGER'], ['name' => 'name', 'type' => 'TEXT'], ['name' => 'major', 'type' => 'TEXT']], 'rows' => [[1, 'Dika', 'Sistem Informasi'], [2, 'Alya', 'Teknik Informatika']]]]]],
            );
            foreach ([
                [1, 'Pilih semua mahasiswa', 'Tampilkan seluruh data mahasiswa.', 'SELECT * FROM students;', ['columns' => ['id', 'name', 'major'], 'rows' => [[1, 'Dika', 'Sistem Informasi'], [2, 'Alya', 'Teknik Informatika']]], 25],
                [2, 'Filter berdasarkan jurusan', 'Tampilkan mahasiswa dari jurusan Sistem Informasi.', "SELECT name, major FROM students WHERE major = 'Sistem Informasi';", ['columns' => ['name', 'major'], 'rows' => [['Dika', 'Sistem Informasi']]], 30],
                [3, 'Urutkan nama', 'Tampilkan nama mahasiswa dari A ke Z.', 'SELECT name FROM students ORDER BY name;', ['columns' => ['name'], 'rows' => [['Alya'], ['Dika']]], 30],
                [4, 'Hitung mahasiswa', 'Hitung jumlah seluruh mahasiswa.', 'SELECT COUNT(*) AS total FROM students;', ['columns' => ['total'], 'rows' => [[2]]], 35],
            ] as [$sort, $title, $instruction, $query, $expected, $xp]) {
                LabStep::updateOrCreate(
                    ['lab_id' => $lab->id, 'sort_order' => $sort],
                    ['title' => $title, 'instruction' => $instruction, 'expected_result_json' => $expected, 'reference_query' => $query, 'xp_reward' => $xp],
                );
            }
        }

        $challengeModules = [
            ['slug' => 'query-sql', 'title' => 'Query Akademik', 'description' => 'SELECT, WHERE, LIKE, ORDER BY, dan LIMIT.', 'schema' => ['tables' => []], 'steps' => []],
            ['slug' => 'fungsi-sql', 'title' => 'Fungsi SQL Akademik', 'description' => 'COUNT, AVG, SUM, dan GROUP BY.', 'schema' => ['tables' => []], 'steps' => []],
        ];
        $challengeModules[0]['schema'] = ['tables' => [['name' => 'unit', 'columns' => [['name' => 'id_unit', 'type' => 'TEXT'], ['name' => 'nama_unit', 'type' => 'TEXT'], ['name' => 'tarif_jam', 'type' => 'INTEGER']], 'rows' => [['U01', 'MIS', 15000], ['U02', 'TechSupport', 10000], ['U03', 'Engineering', 20000], ['U04', 'TechDoc', 7500]]]]];
        $challengeModules[0]['steps'] = [
            [1, 'Tampilkan semua unit', 'Tampilkan id_unit dan nama_unit.', 'SELECT id_unit, nama_unit FROM unit;', ['columns' => ['id_unit', 'nama_unit'], 'rows' => [['U01', 'MIS'], ['U02', 'TechSupport'], ['U03', 'Engineering'], ['U04', 'TechDoc']]], 20],
            [2, 'Filter tarif murah', 'Tampilkan unit dengan tarif di bawah 15000.', 'SELECT id_unit, nama_unit, tarif_jam FROM unit WHERE tarif_jam < 15000;', ['columns' => ['id_unit', 'nama_unit', 'tarif_jam'], 'rows' => [['U02', 'TechSupport', 10000], ['U04', 'TechDoc', 7500]]], 25],
            [3, 'Cari nama unit', "Tampilkan unit yang namanya diawali 'Tech'.", "SELECT nama_unit FROM unit WHERE nama_unit LIKE 'Tech%';", ['columns' => ['nama_unit'], 'rows' => [['TechSupport'], ['TechDoc']]], 25],
            [4, 'Batasi hasil', 'Tampilkan dua unit dengan tarif tertinggi.', 'SELECT nama_unit, tarif_jam FROM unit ORDER BY tarif_jam DESC LIMIT 2;', ['columns' => ['nama_unit', 'tarif_jam'], 'rows' => [['Engineering', 20000], ['MIS', 15000]]], 30],
        ];
        $challengeModules[1]['schema'] = ['tables' => [['name' => 'matakuliah', 'columns' => [['name' => 'kode_kul', 'type' => 'TEXT'], ['name' => 'nama_kul', 'type' => 'TEXT'], ['name' => 'sks', 'type' => 'INTEGER'], ['name' => 'smt', 'type' => 'INTEGER']], 'rows' => [['DB101', 'Basis Data', 3, 5], ['SQL201', 'SQL Lanjut', 3, 5], ['ERD101', 'Model Data', 2, 4], ['WEB101', 'Web', 3, 4]]]]];
        $challengeModules[1]['steps'] = [
            [1, 'Hitung mata kuliah', 'Hitung jumlah seluruh mata kuliah.', 'SELECT COUNT(*) AS total FROM matakuliah;', ['columns' => ['total'], 'rows' => [[4]]], 25],
            [2, 'Rata-rata SKS', 'Hitung rata-rata SKS semester 5.', 'SELECT AVG(sks) AS rata_rata FROM matakuliah WHERE smt = 5;', ['columns' => ['rata_rata'], 'rows' => [[3]]], 25],
            [3, 'Total SKS', 'Hitung total SKS tiap semester.', 'SELECT smt, SUM(sks) AS total_sks FROM matakuliah GROUP BY smt ORDER BY smt;', ['columns' => ['smt', 'total_sks'], 'rows' => [[4, 5], [5, 6]]], 30],
        ];
        $challengeModules[] = ['slug' => 'sql-dasar', 'title' => 'DDL dan DML Toko Komputer', 'description' => 'Kelola stok produk toko komputer dengan CREATE, INSERT, UPDATE, dan DELETE.', 'schema' => ['tables' => [['name' => 'produk', 'columns' => [['name' => 'id', 'type' => 'INTEGER'], ['name' => 'nama', 'type' => 'TEXT'], ['name' => 'stok', 'type' => 'INTEGER']], 'rows' => [[1, 'Keyboard', 10], [2, 'Mouse', 5]]]]], 'steps' => [[1, 'Cek stok', 'Tampilkan seluruh produk.', 'SELECT * FROM produk;', ['columns' => ['id', 'nama', 'stok'], 'rows' => [[1, 'Keyboard', 10], [2, 'Mouse', 5]]], 20]]];
        $challengeModules[] = ['slug' => 'teknik-join', 'title' => 'JOIN Nilai Mahasiswa', 'description' => 'Gabungkan data mahasiswa dengan nilai untuk membuat laporan akademik.', 'schema' => ['tables' => [['name' => 'mahasiswa', 'columns' => [['name' => 'nrp', 'type' => 'TEXT'], ['name' => 'nama', 'type' => 'TEXT']], 'rows' => [['M01', 'Alya'], ['M02', 'Dika']]], ['name' => 'nilai', 'columns' => [['name' => 'nrp', 'type' => 'TEXT'], ['name' => 'nilai', 'type' => 'INTEGER']], 'rows' => [['M01', 90], ['M02', 80]]]]], 'steps' => [[1, 'Buat laporan nilai', 'Tampilkan nama dan nilai mahasiswa.', 'SELECT m.nama, n.nilai FROM mahasiswa m JOIN nilai n ON n.nrp = m.nrp;', ['columns' => ['nama', 'nilai'], 'rows' => [['Alya', 90], ['Dika', 80]]], 40]]];
        $challengeModules[] = ['slug' => 'keamanan-database', 'title' => 'Audit Saldo Bank', 'description' => 'Gunakan query untuk menemukan saldo terbesar sebagai bagian dari audit data.', 'schema' => ['tables' => [['name' => 'akun', 'columns' => [['name' => 'id', 'type' => 'INTEGER'], ['name' => 'saldo', 'type' => 'INTEGER']], 'rows' => [[1, 100000], [2, 50000]]]]], 'steps' => [[1, 'Audit saldo terbesar', 'Tampilkan akun dengan saldo terbesar.', 'SELECT * FROM akun ORDER BY saldo DESC LIMIT 1;', ['columns' => ['id', 'saldo'], 'rows' => [[1, 100000]]], 30]]];
        foreach ($challengeModules as $challenge) {
            $module = $course->modules->firstWhere('slug', $challenge['slug']);
            if (! $module) continue;
            $lab = Lab::updateOrCreate(['module_id' => $module->id, 'title' => $challenge['title']], ['description' => $challenge['description'], 'mode' => 'challenge', 'sort_order' => 1, 'is_published' => true, 'schema_json' => $challenge['schema']]);
            foreach ($challenge['steps'] as $index => [, $title, $instruction, $query, $expected, $xp]) {
                LabStep::updateOrCreate(['lab_id' => $lab->id, 'sort_order' => $index + 1], ['title' => $title, 'instruction' => $instruction, 'expected_result_json' => $expected, 'reference_query' => $query, 'xp_reward' => $xp]);
            }
        }

        $designModule = $course->modules->firstWhere('slug', 'model-data-erd') ?? $course->modules->firstWhere('slug', 'transformasi-erd');
        if ($designModule) {
            $puzzles = [
                ['title' => 'Cat Store', 'case_text' => 'Pisahkan pelanggan, barang, pesanan, dan detail penjualan agar data tidak berulang.', 'entities' => [['Pelanggan', ['id_pelanggan', 'nama', 'telepon', 'alamat']], ['Barang', ['id_barang', 'nama', 'stok', 'harga']], ['Pesanan', ['id_pesanan', 'tanggal', 'id_pelanggan']], ['Detail Pesanan', ['id_pesanan', 'id_barang', 'jumlah']]], 'relations' => [['Pelanggan', 'Pesanan', '1:N'], ['Pesanan', 'Detail Pesanan', '1:N'], ['Barang', 'Detail Pesanan', '1:N']]],
                ['title' => 'Rental Mobil', 'case_text' => 'Rancang database rental untuk pelanggan, mobil, dan transaksi penyewaan.', 'entities' => [['Pelanggan', ['id_pelanggan', 'nama', 'telepon']], ['Mobil', ['id_mobil', 'plat_nomor', 'tipe', 'tarif_harian']], ['Sewa', ['id_sewa', 'id_pelanggan', 'id_mobil', 'mulai', 'selesai']]], 'relations' => [['Pelanggan', 'Sewa', '1:N'], ['Mobil', 'Sewa', '1:N']]],
                ['title' => 'Akademik', 'case_text' => 'Hubungkan mahasiswa dan mata kuliah melalui tabel KRS.', 'entities' => [['Mahasiswa', ['nrp', 'nama', 'id_prodi']], ['Mata Kuliah', ['kode_kul', 'nama_kul', 'sks']], ['KRS', ['nrp', 'kode_kul', 'semester', 'nilai']]], 'relations' => [['Mahasiswa', 'KRS', '1:N'], ['Mata Kuliah', 'KRS', '1:N']]],
            ];
            foreach ($puzzles as $puzzleData) {
                $puzzle = ErdPuzzle::updateOrCreate(['module_id' => $designModule->id, 'title' => $puzzleData['title']], ['case_text' => $puzzleData['case_text'], 'instructions' => 'Pilih entity dan relasi yang sesuai.', 'max_score' => 100, 'xp_reward' => 50, 'is_published' => true]);
                $components = [];
                foreach ($puzzleData['entities'] as $sort => [$name, $attributes]) {
                    $components[$name] = ErdComponent::updateOrCreate(['erd_puzzle_id' => $puzzle->id, 'name' => $name], ['type' => 'entity', 'sort_order' => $sort + 1, 'definition_json' => ['attributes' => $attributes]]);
                }
                foreach ($puzzleData['relations'] as [$from, $to, $cardinality]) {
                    ErdRelation::updateOrCreate(['erd_puzzle_id' => $puzzle->id, 'name' => $from.' → '.$to], ['definition_json' => ['from' => $components[$from]->id, 'to' => $components[$to]->id, 'cardinality' => $cardinality]]);
                }
            }
        }

        foreach ($course->modules as $module) {
            $quiz = Quiz::updateOrCreate(
                ['module_id' => $module->id],
                ['title' => 'Kuis '.$module->title, 'passing_score' => 70, 'max_xp' => 100, 'is_published' => true],
            );
            $questions = match ($module->slug) {
                'sql-dasar', 'query-sql', 'fungsi-sql', 'teknik-join' => [
                    ['Apa fungsi WHERE dalam SQL?', 'Menyaring baris berdasarkan kondisi.', ['Menyaring baris berdasarkan kondisi.', 'Menghapus tabel.', 'Membuat user baru.']],
                    ['Klausa apa yang mengurutkan hasil query?', 'ORDER BY mengurutkan hasil.', ['ORDER BY', 'GROUP BY', 'HAVING']],
                    ['Apa fungsi COUNT(*)?', 'COUNT menghitung jumlah baris.', ['Menghitung jumlah baris.', 'Mengubah nama tabel.', 'Membuat relasi.']],
                ],
                'model-data-erd', 'normalisasi-database', 'transformasi-erd' => [
                    ['Apa tujuan normalisasi?', 'Normalisasi mengurangi redundansi dan anomali.', ['Mengurangi duplikasi data.', 'Memperbesar ukuran tabel.', 'Menghapus semua key.']],
                    ['Apa fungsi primary key?', 'Primary key mengidentifikasi record secara unik.', ['Mengidentifikasi record secara unik.', 'Menyimpan password.', 'Mengurutkan tabel otomatis.']],
                ],
                default => [
                    ['Apa manfaat utama database?', 'Database menyimpan data terstruktur agar mudah dikelola.', ['Menyimpan data terstruktur.', 'Menggantikan sistem operasi.', 'Menghapus kebutuhan validasi.']],
                    ['Apa yang dimaksud data?', 'Data adalah fakta yang dapat diolah menjadi informasi.', ['Fakta yang dapat diolah.', 'Hanya gambar.', 'Selalu berupa angka.']],
                ],
            };
            foreach ($questions as $questionIndex => [$questionText, $explanation, $options]) {
                $sort = $questionIndex + 1;
                $question = Question::updateOrCreate(
                    ['quiz_id' => $quiz->id, 'sort_order' => $sort],
                    ['type' => 'multiple_choice', 'question' => $questionText, 'explanation' => $explanation, 'points' => 1],
                );
                foreach ($options as $optionIndex => $optionText) {
                    QuestionOption::updateOrCreate(['question_id' => $question->id, 'sort_order' => $optionIndex + 1], ['option_text' => $optionText, 'is_correct' => $optionIndex === 0]);
                }
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
