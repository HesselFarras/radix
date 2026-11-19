# PRD Radix — Learning Platform Interaktif Basis Data

Oct 17, 2026 · Disusun oleh @jay · Tim Radix, Teknik Informatika

## 1. Overview

### 1.1 Latar Belakang

Mahasiswa Teknik Informatika kesulitan mempraktikkan materi Basis Data secara mandiri karena dua hambatan utama: (1) eksekusi query SQL biasanya butuh instalasi software berat atau server yang tidak selalu tersedia tanpa instalasi tambahan, dan (2) materi teoritis (ERD, normalisasi, model data) bersifat abstrak dan sulit divisualisasikan tanpa latihan interaktif. Radix dibangun sebagai Progressive Web App yang menjawab kedua hambatan ini: ringan, bisa diakses offline, dan menjalankan SQL langsung di browser tanpa server.

### 1.2 Visi Produk

Radix adalah "fondasi digital" untuk belajar Basis Data — platform yang memungkinkan mahasiswa memahami konsep database, mempraktikkan SQL, dan merancang ERD kapan saja, di mana saja, bahkan tanpa koneksi internet. Nama Radix (bahasa Latin untuk "akar"; juga istilah basis sistem bilangan) mencerminkan filosofi produk: database adalah fondasi tak terlihat yang menopang setiap aplikasi.

### 1.3 Target Pengguna

| Atribut | Deskripsi |
| --- | --- |
| Pengguna utama | Mahasiswa Teknik Informatika yang mengambil matkul Basis Data |
| Perangkat | Laptop/PC dengan browser modern (perangkat utama); ponsel tetap didukung penuh untuk SQL Lab dan Challenge via PWA offline |
| Konteks pemakaian | Belajar mandiri di luar jam kuliah, revisi sebelum kuis/ujian, praktik SQL tanpa perlu install software |
| Level teknis | Pemula–menengah dalam database; familiar dengan antarmuka mobile app umum |

### 1.4 Tujuan & Success Metrics

- Mahasiswa dapat menyelesaikan modul materi secara berjenjang tanpa tersesat navigasi
- Mahasiswa dapat menjalankan dan memahami hasil query SQL secara langsung (hands-on, bukan cuma baca teori)
- Engagement meningkat lewat gamifikasi (ERD Puzzle, Query Challenge, XP, badge)
- Metrik keberhasilan: jumlah modul diselesaikan per pengguna, skor rata-rata kuis, jumlah percobaan Query Challenge, tingkat penyelesaian seluruh kursus (completion rate) hingga mendapat sertifikat

## 2. Scope

### 2.1 In-Scope (Fase Mockup)

- Modul materi dasar (representatif, bukan 16 pertemuan penuh) dengan sistem reader berjenjang
- SQL Lab dengan dua mode: Practice (latihan bebas) dan Challenge (bernilai skor/XP)
- ERD Puzzle sebagai gamifikasi terpisah, bernilai skor/XP
- Kuis per modul
- Sistem XP, badge, leaderboard global, dan sertifikat
- Onboarding tour dengan avatar maskot (5 langkah)
- Autentikasi dasar (login/register) — khusus untuk kebutuhan tampilan UI, bukan desain sistem backend
- Profil pengguna, Pengaturan (tema, ukuran font), halaman Tentang/Bantuan
- Desain mendukung mode gelap dan terang, serta responsif desktop-first, dengan SQL Lab dan ERD Puzzle tetap didukung penuh di mobile (termasuk offline via PWA)

* Admin panel di route `/admin/` (codebase sama, akses berbasis role), responsif mobile/PWA sama seperti sisi mahasiswa
* Content management untuk admin: Materi, Studi Kasus SQL Lab, Studi Kasus ERD Puzzle, Bank Soal Kuis, Kriteria Badge
* Manajemen data pengguna (User Management) termasuk kontrol tampilan Leaderboard

### 2.2 Belum Dibahas pada Dokumen Ini

Bagian-bagian berikut **bukan dihilangkan dari produk**, melainkan belum dirinci di dokumen PRD tahap mockup ini dan akan diperdalam pada revisi PRD berikutnya seiring masuk ke fase development:

- Arsitektur backend & database produksi, skema API
- Autentikasi, keamanan, dan manajemen sesi pengguna
- Sinkronisasi data lintas device
- Pemetaan detail 16 pertemuan RPS ke modul aplikasi

PRD ini disusun agar scalable — struktur fitur, IA, dan requirement yang sudah ditetapkan tetap jadi fondasi saat bagian-bagian di atas dirinci lebih lanjut, bukan dokumen yang berdiri sendiri dan terpisah dari arah produk ke depan.

### 2.3 Asumsi & Batasan

- Aplikasi berjalan sebagai PWA — dapat dipasang di layar utama, mendukung mode offline untuk materi yang sudah diunduh
- Eksekusi SQL dilakukan di sisi klien (tanpa server khusus), sehingga Lab SQL harus didesain agar pengguna memahami bahwa ini adalah simulasi lokal
- Target perangkat utama adalah desktop/laptop dengan browser modern; ponsel tetap didukung penuh sebagai pengalaman sekunder, khususnya untuk SQL Lab dan Challenge yang dapat diakses offline lewat PWA

## 3. Persona & User Journey

### 3.1 Persona Utama — "Dika"

Mahasiswa semester 3 Teknik Informatika, sedang mengambil matkul Basis Data. Laptop jadi perangkat utama belajar, terutama untuk SQL Lab yang butuh fokus dan keyboard; tetap mengakses lewat ponsel saat di kampus atau perjalanan, termasuk saat sinyal lemah — SQL Lab dan Challenge tetap bisa diakses offline lewat PWA. Butuh cara cepat memahami SQL dan ERD tanpa install software berat, dan termotivasi oleh progres yang terlihat (XP, badge) dibanding sekadar membaca modul PDF panjang.

### 3.2 Persona Admin — Tim Development

Tim development yang mengelola konten dan data platform lewat route `/admin/` (login lewat form yang sama dengan mahasiswa, diarahkan otomatis berdasarkan role). Akun admin tidak dibuat lewat form Daftar publik — disiapkan lewat seeding awal atau diberikan lewat User Management. Di luar area admin, akun ini tetap berjalan sebagai pengguna biasa — bisa mengikuti modul, mengumpulkan XP, dan tampil di Leaderboard seperti mahasiswa lain.

### 3.3 User Journey Ringkas

1. Dika memasang Radix dari browser ke layar utama ponselnya (install PWA)
2. Membuka aplikasi pertama kali → disambut avatar maskot, mengikuti tur onboarding 5 langkah (tanpa perlu akun)
3. Menjelajah Dashboard dalam mode preview, melihat peta modul yang harus diselesaikan berurutan
4. Membuka modul pertama di Module Reader, membaca materi, memberi highlight pada bagian penting
5. Saat mencoba masuk ke SQL Lab untuk pertama kali, Dika diminta Login/Register
6. Setelah login, Dika mencoba studi kasus di SQL Lab (Practice Mode) untuk memahami query secara langsung
7. Mengulangi 2–3 topik berikutnya dalam modul yang sama, lalu mengerjakan Kuis Modul
8. Jika skor kuis di bawah batas lulus, Dika diarahkan kembali membaca modul sebelum mencoba kuis lagi
9. Setelah lulus, Dika mencoba Query Challenge dan ERD Puzzle untuk mengumpulkan XP tambahan dan naik di Leaderboard
10. Dika memantau progres, badge, dan XP di halaman Profil
11. Setelah semua modul selesai, Dika mengunduh sertifikat kelulusan

## 4. Information Architecture

### 4.1 Daftar Halaman

| # | Halaman | Diakses Dari |
| --- | --- | --- |
| 1 | Onboarding/Tour | Pertama kali buka app |
| 2 | Login/Register | Sebelum masuk Dashboard |
| 3 | Dashboard/Beranda | Nav bawah (home) |
| 4 | Module Reader | Dashboard → pilih modul |
| 5 | SQL Lab (Practice & Challenge) | Dashboard / Module Reader |
| 6 | ERD Puzzle | Dashboard / nav gamifikasi |
| 7 | Kuis Modul | Akhir Module Reader |
| 8 | Leaderboard | Nav bawah |
| 9 | Profil | Nav bawah |
| 10 | Pengaturan | Profil → ikon gear |
| 11 | Tentang/Bantuan | Profil / menu |

**Halaman Admin (route `/admin/`)**

| # | Halaman Admin | Fungsi |
| --- | --- | --- |
| A1 | Admin Dashboard | Overview umum platform (jumlah user, modul, aktivitas) |
| A2 | Materi Management | CRUD modul & topik |
| A3 | SQL Lab Management | Kelola studi kasus, skema, expected query |
| A4 | ERD Puzzle Management | Kelola studi kasus, entity/attribute/relationship valid |
| A5 | Kuis Management | Bank soal (pilihan ganda & isian), kunci jawaban, batas skor lulus |
| A6 | Badge Management | Kriteria, ikon (placeholder), deskripsi badge |
| A7 | User Management | Data pengguna, progres, role; termasuk tab kontrol Leaderboard |

Seluruh halaman admin responsif mobile/PWA, mengikuti aturan breakpoint yang sama dengan sisi mahasiswa.

### 4.2 Model Navigasi

Radix memakai navbar horizontal di desktop/tablet sebagai pola navigasi utama, dengan reflow menjadi bottom navigation di mobile (<768px) — konsisten di semua layar (sesuai WCAG 3.2.3 — navigasi konsisten):

- **Navbar (desktop/tablet):** akses cepat ke Dashboard, SQL Lab, Leaderboard, dan Profil, posisi tetap di bagian atas
- **Bottom navigation (mobile):** hasil reflow dari navbar, 4 destinasi yang sama, posisi tetap di bagian bawah
- **Tombol Back (panah kiri, pojok kiri atas):** selalu mundur satu layar, tidak pernah lompat ke menu utama, konsisten di semua breakpoint. Pada layar dengan progres belum tersimpan (Lab atau Kuis berjalan), Back memicu dialog konfirmasi agar progres tidak hilang begitu saja

Struktur ini mengikuti hierarki tugas yang sudah ditetapkan pada dokumen HTA (lihat Bagian 7).

## 5. Fitur & Requirement per Halaman

### 5.1 Onboarding/Tour

Memperkenalkan Radix lewat avatar maskot dalam 5 langkah (Peta modul, Reader, Lab SQL, Kuis & badge, Profil). **Dapat diakses tanpa akun** — muncul otomatis saat pertama kali buka app, sebelum login. Elemen: speech bubble, indikator langkah, tombol Lanjut/Lewati. Requirement: bisa dilewati kapan saja, bisa dinavigasi via keyboard/pembaca layar, bisa diulang dari Profil.

### 5.2 Login/Register

**Dipicu kontekstual**, bukan gerbang wajib di awal — muncul saat pengguna mencoba mengakses fitur yang butuh akun (SQL Lab, Kuis, ERD Puzzle, Leaderboard, Profil) sambil masih dalam mode preview/onboarding. Elemen: email/username, kata sandi, toggle ke mode daftar, input dengan label jelas, pesan error spesifik (bukan generik), tombol submit, link "lupa kata sandi". Requirement: kolom sandi boleh ditempel (paste) dan dikenali pengelola sandi (WCAG 3.3.8), label dan instruksi jelas di tiap kolom (WCAG 3.3.2). Satu form login dipakai untuk semua role — pengguna diarahkan otomatis ke Dashboard (mahasiswa) atau Admin Dashboard (admin) sesuai role akun. Akun admin tidak dibuat lewat toggle Daftar publik ini.

### 5.3 Dashboard/Beranda

Menampilkan peta modul (urutan belajar) dan ringkasan progres. Elemen: kartu modul dengan status (belum/berjalan/selesai), progress bar keseluruhan, XP terkini, shortcut ke SQL Lab dan ERD Puzzle. Requirement: status modul tidak hanya diwakili warna, tapi juga ikon + teks (WCAG 1.4.1).

### 5.4 Module Reader

Menampilkan materi per topik dengan navigasi antar topik dalam modul. Elemen: konten teks, tombol highlight/bookmark, tombol "Jawab Check Your Understanding", pengaturan ukuran font. Requirement: ukuran font bisa diperbesar hingga 200% (WCAG 1.4.4), kontras teks isi memenuhi 4,5:1 (WCAG 1.4.3).

### 5.5 SQL Lab (Practice Mode & Challenge Mode)

**Practice Mode:** latihan bebas sesuai studi kasus modul, tanpa skor/waktu, fokus eksplorasi. **Challenge Mode:** target soal dengan skor dan XP, hasil benar/salah langsung terlihat. Elemen bersama: editor SQL dengan tombol pintas simbol `( ) ; * ' =`, tombol Run, panel hasil query, pesan error spesifik (misal "Titik koma belum ada"), feedback XP via aria-live. Requirement: dapat dioperasikan penuh lewat keyboard, fokus elemen terlihat jelas (WCAG 2.4.7), target sentuh tombol utama minimal 44px.

### 5.6 ERD Puzzle

Tantangan drag-and-drop menyusun entity, atribut, dan relasi dari sebuah studi kasus menjadi ERD yang benar. Elemen: kanvas puzzle, panel komponen (entity/atribut/relasi) yang bisa ditarik, tombol cek jawaban, skor & XP. Requirement: tersedia alternatif non-drag (misal pilih lalu tempatkan via tombol) untuk aksesibilitas keyboard (WCAG 2.1.1).

### 5.7 Kuis Modul

Soal pilihan ganda/isian terkait modul yang baru dipelajari. Elemen: soal bertahap, progres soal (3/10), tombol kirim jawaban, halaman skor & pembahasan. Requirement: jika skor di bawah batas lulus, pengguna diarahkan kembali ke materi (bukan diblokir), sesuai aturan plan pada HTA Tugas 4.

### 5.8 Leaderboard

Peringkat XP global antar pengguna, tampilan all-time (akumulasi XP sepanjang waktu, tanpa filter periode). Elemen: daftar peringkat (nama, XP, posisi), highlight posisi pengguna sendiri. Requirement: kontras teks dan baris peringkat pengguna sendiri harus jelas dibedakan tanpa hanya mengandalkan warna.

### 5.9 Profil

Ringkasan pencapaian pengguna. Elemen: XP total, daftar badge, progres sertifikat, riwayat modul selesai, tombol ulangi tur onboarding. Requirement: sertifikat baru dapat diunduh setelah semua modul selesai (HTA Tugas 5, Plan 5).

### 5.10 Pengaturan

Elemen: toggle mode gelap/terang, pengaturan ukuran font, pengaturan bahasa (default Indonesia), tombol ulangi tur. Requirement: perubahan tema/font langsung terlihat efeknya tanpa reload penuh.

### 5.11 Tentang/Bantuan

Elemen: penjelasan singkat Radix, FAQ, kontak/dukungan, versi aplikasi. Requirement: bahasa sederhana, terstruktur dengan heading yang jelas untuk navigasi pembaca layar.

### 5.12 Admin Dashboard

Overview umum platform untuk tim development. Elemen: ringkasan jumlah pengguna aktif, modul tersedia, rata-rata skor kuis, completion rate. Requirement: data agregat saja (bukan data individual mendalam — detail per pengguna ada di User Management).

### 5.13 Materi Management

CRUD modul dan topik materi. Elemen: tabel daftar modul/topik dengan pencarian, form tambah/edit (judul, urutan, konten), tombol hapus dengan dialog konfirmasi.

### 5.14 SQL Lab Management

Kelola studi kasus SQL Lab. Elemen: tabel daftar studi kasus, form tambah/edit (nama kasus, skema starter, expected query untuk Challenge Mode), dialog konfirmasi hapus.

### 5.15 ERD Puzzle Management

Kelola studi kasus ERD Puzzle. Elemen: tabel daftar studi kasus, form tambah/edit (entity, attribute, relationship yang valid sebagai kunci jawaban), dialog konfirmasi hapus.

### 5.16 Kuis Management

Kelola bank soal kuis. Elemen: tabel daftar soal per modul, form tambah/edit yang mendukung dua tipe soal (pilihan ganda dan isian), pengaturan kunci jawaban dan batas skor lulus per kuis.

### 5.17 Badge Management

Kelola kriteria dan tampilan badge. Elemen: tabel daftar badge, form tambah/edit (nama, deskripsi, kriteria pemicu, ikon sebagai placeholder — konsisten dengan aturan placeholder logo/avatar).

### 5.18 User Management

Kelola data pengguna. Elemen: tabel daftar pengguna dengan pencarian, detail progres & XP per pengguna (tampilan, bukan field edit bebas), aksi terbatas (reset progres modul tertentu, kecualikan dari Leaderboard, nonaktifkan akun), serta tab terpisah untuk kontrol tampilan Leaderboard. Requirement: tidak ada edit XP/skor secara manual/bebas — hanya aksi-aksi terbatas yang tercatat jelas alasannya.

## 6. Gamifikasi — Aturan Umum

### 6.1 Sumber XP

| Sumber | Kapan Didapat |
| --- | --- |
| Kuis Modul | Setelah submit jawaban, sesuai skor |
| Query Challenge | Per soal berhasil diselesaikan dengan benar |
| ERD Puzzle | Per puzzle berhasil disusun dengan benar |

### 6.2 Badge

Badge diberikan atas pencapaian tertentu, contoh: menyelesaikan modul pertama, lulus kuis tanpa mengulang, menyelesaikan seluruh Query Challenge satu modul, menyusun ERD Puzzle tanpa kesalahan. Daftar lengkap kriteria badge didetailkan saat desain konten.

### 6.3 Leaderboard Global

Peringkat berbasis akumulasi XP dari seluruh sumber (kuis, Query Challenge, ERD Puzzle). Posisi pengguna sendiri selalu ditonjolkan meskipun berada jauh di bawah daftar teratas.

### 6.4 Sertifikat

Tersedia untuk diunduh setelah seluruh modul dalam kursus diselesaikan (mengikuti Plan 5.2 pada HTA: "jika semua modul selesai").

## 7. HTA Reference

Struktur fitur pada PRD ini mengacu langsung pada dokumen Hierarchical Task Analysis (HTA) "Belajar Basis Data lewat Radix" yang telah disusun tim. Lima tugas utama level 1: (1) Masuk ke aplikasi, (2) Mempelajari materi modul, (3) Mengerjakan lab SQL, (4) Mengerjakan kuis modul, (5) Melihat progres & hadiah — dengan tombol Back (mundur satu layar) tersedia di semua tugas. Detail plan, aturan siklus, dan percabangan tiap tugas tidak diulang di sini; rujuk dokumen HTA sebagai sumber kebenaran untuk alur interaksi.

## 8. Accessibility Requirement (WCAG 2.2 AA)

Seluruh mockup dan desain antarmuka wajib mencerminkan target level AA, mengacu pada checklist yang telah disusun tim. Poin kunci yang wajib terlihat di mockup:

| Prinsip | Aturan Kunci | Penerapan di Mockup |
| --- | --- | --- |
| Dapat dipersepsi | Kontras teks 4,5:1; warna bukan satu-satunya penanda status | Gunakan palet Bedrock/Graphite untuk teks isi, bukan Signal terang |
| Dapat dioperasikan | Target sentuh ≥44px; fokus terlihat jelas | Tombol utama minimal 44px; outline fokus tebal pada elemen aktif |
| Dapat dipahami | Navigasi konsisten; pesan error jelas | Navbar (desktop)/bottom nav (mobile) & tombol Back konsisten posisinya tiap layar |
| Tangguh | Struktur semantik (judul, label, tombol) | Komponen kustom (progress, tab, dialog) menggunakan atribut ARIA |

Detail lengkap checklist WCAG merujuk dokumen "WCAG 2.2 untuk web Radix" yang telah disusun tim.

## 9. Avatar Maskot — Peran dalam Produk

Avatar maskot Radix (turunan bentuk logo) berperan dalam dua konteks:

1. **Onboarding tour (wajib):** memandu pengguna baru lewat 5 langkah pengenalan fitur utama saat pertama kali membuka aplikasi, bisa dilewati/diulang kapan saja dari Profil.
2. **Feedback kontekstual:** muncul dalam ukuran kecil di pojok layar pada momen-momen kunci — reaksi positif saat query benar atau kuis lulus, reaksi netral/menunjukkan kebingungan saat query salah atau jawaban kuis kurang tepat, reaksi netral saat memuat hasil/proses berjalan.

Aset visual avatar akan dipasang manual oleh tim pada tahap desain — untuk mockup, posisi avatar dan logo cukup ditandai sebagai placeholder.

## 10. Belum Dibahas pada Sesi Ini (Bukan Dihapus dari Roadmap)

Dokumen PRD ini sengaja dibatasi untuk mendukung fase Design System dan mockup. Bagian berikut pasti menjadi bagian dari Radix ke depan, tapi detailnya disusun di revisi PRD lanjutan setelah fase mockup selesai:

- Arsitektur backend/database teknis, autentikasi & keamanan mendalam, sinkronisasi lintas device
- Pemetaan detail 16 pertemuan RPS ke modul aplikasi
- Implementasi kode/development aktual
- Daftar lengkap kriteria badge & bank soal kuis/challenge

Fokus dokumen versi ini: PRD, Design System, dan mockup visual (Google Stitch → Figma) sebagai dasar komunikasi desain — bukan batas akhir fitur produk. PRD ini akan di-upgrade seiring project berjalan.
