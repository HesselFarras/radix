# Kunci Jawaban Radix

Dokumen ini memuat kunci jawaban konten yang dibuat oleh `DatabaseSeeder`.

## Kuis

Jawaban benar selalu opsi pertama pada setiap soal.

### Modul SQL Dasar, Query SQL, Fungsi SQL, Teknik JOIN

1. **Apa fungsi WHERE dalam SQL?**  
   Jawaban: Menyaring baris berdasarkan kondisi.
2. **Klausa apa yang mengurutkan hasil query?**  
   Jawaban: `ORDER BY`.
3. **Apa fungsi COUNT(*)?**  
   Jawaban: Menghitung jumlah baris.

### Modul Model Data dan ERD, Normalisasi Database, Transformasi ERD

1. **Apa tujuan normalisasi?**  
   Jawaban: Mengurangi duplikasi data.
2. **Apa fungsi primary key?**  
   Jawaban: Mengidentifikasi record secara unik.

### Modul lainnya

1. **Apa manfaat utama database?**  
   Jawaban: Menyimpan data terstruktur.
2. **Apa yang dimaksud data?**  
   Jawaban: Fakta yang dapat diolah.

## SQL Lab

### Teknik Query SQL

Schema: `students(id, name, major)`.

1. **Pilih semua mahasiswa**
   ```sql
   SELECT * FROM students;
   ```
   Hasil: `(1, Dika, Sistem Informasi)`, `(2, Alya, Teknik Informatika)`.

2. **Filter berdasarkan jurusan**
   ```sql
   SELECT name, major
   FROM students
   WHERE major = 'Sistem Informasi';
   ```
   Hasil: `(Dika, Sistem Informasi)`.

3. **Urutkan nama**
   ```sql
   SELECT name FROM students ORDER BY name;
   ```
   Hasil: `Alya`, `Dika`.

4. **Hitung mahasiswa**
   ```sql
   SELECT COUNT(*) AS total FROM students;
   ```
   Hasil: `total = 2`.

### Query Akademik

Schema: `unit(id_unit, nama_unit, tarif_jam)`.

1. ```sql
   SELECT id_unit, nama_unit FROM unit;
   ```
   Hasil: `U01/MIS`, `U02/TechSupport`, `U03/Engineering`, `U04/TechDoc`.

2. ```sql
   SELECT id_unit, nama_unit, tarif_jam
   FROM unit
   WHERE tarif_jam < 15000;
   ```
   Hasil: `U02/TechSupport/10000`, `U04/TechDoc/7500`.

3. ```sql
   SELECT nama_unit
   FROM unit
   WHERE nama_unit LIKE 'Tech%';
   ```
   Hasil: `TechSupport`, `TechDoc`.

4. ```sql
   SELECT nama_unit, tarif_jam
   FROM unit
   ORDER BY tarif_jam DESC
   LIMIT 2;
   ```
   Hasil: `Engineering/20000`, `MIS/15000`.

### Fungsi SQL Akademik

Schema: `matakuliah(kode_kul, nama_kul, sks, smt)`.

1. ```sql
   SELECT COUNT(*) AS total FROM matakuliah;
   ```
   Hasil: `total = 4`.

2. ```sql
   SELECT AVG(sks) AS rata_rata
   FROM matakuliah
   WHERE smt = 5;
   ```
   Hasil: `rata_rata = 3`.

3. ```sql
   SELECT smt, SUM(sks) AS total_sks
   FROM matakuliah
   GROUP BY smt
   ORDER BY smt;
   ```
   Hasil: semester `4 = 5 SKS`, semester `5 = 6 SKS`.

### DDL dan DML Toko Komputer

Schema: `produk(id, nama, stok)`.

```sql
SELECT * FROM produk;
```

Hasil: `1/Keyboard/10`, `2/Mouse/5`.

### JOIN Nilai Mahasiswa

Schema: `mahasiswa(nrp, nama)` dan `nilai(nrp, nilai)`.

```sql
SELECT m.nama, n.nilai
FROM mahasiswa m
JOIN nilai n ON n.nrp = m.nrp;
```

Hasil: `Alya/90`, `Dika/80`.

### Audit Saldo Bank

Schema: `akun(id, saldo)`.

```sql
SELECT *
FROM akun
ORDER BY saldo DESC
LIMIT 1;
```

Hasil: `id = 1`, `saldo = 100000`.

## ERD Puzzle

Semua component entity yang tersedia pada kasus merupakan jawaban benar. Semua relation yang tercantum merupakan jawaban benar. Cardinality harus sesuai.

### Cat Store

**Entity:**

- Pelanggan: `id_pelanggan`, `nama`, `telepon`, `alamat`
- Barang: `id_barang`, `nama`, `stok`, `harga`
- Pesanan: `id_pesanan`, `tanggal`, `id_pelanggan`
- Detail Pesanan: `id_pesanan`, `id_barang`, `jumlah`

**Relation:**

- Pelanggan → Pesanan: `1:N`
- Pesanan → Detail Pesanan: `1:N`
- Barang → Detail Pesanan: `1:N`

### Rental Mobil

**Entity:**

- Pelanggan: `id_pelanggan`, `nama`, `telepon`
- Mobil: `id_mobil`, `plat_nomor`, `tipe`, `tarif_harian`
- Sewa: `id_sewa`, `id_pelanggan`, `id_mobil`, `mulai`, `selesai`

**Relation:**

- Pelanggan → Sewa: `1:N`
- Mobil → Sewa: `1:N`

### Akademik

**Entity:**

- Mahasiswa: `nrp`, `nama`, `id_prodi`
- Mata Kuliah: `kode_kul`, `nama_kul`, `sks`
- KRS: `nrp`, `kode_kul`, `semester`, `nilai`

**Relation:**

- Mahasiswa → KRS: `1:N`
- Mata Kuliah → KRS: `1:N`

## Catatan implementasi

- Kuis menandai opsi dengan `is_correct = true`.
- SQL Lab menilai hasil berdasarkan `expected_result_json`, bukan teks query saja.
- ERD Puzzle menilai component, relation, dan cardinality.
- Seeder memakai `updateOrCreate`; menjalankan seed tidak menghapus attempt learner.
