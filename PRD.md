# PRD — MindCheck
Sistem Pengecekan Tingkat Stress Siswa berbasis PSS-10

Versi: 0.2 (final — seluruh keputusan desain terkonfirmasi)
Tanggal: 2026-08-04

---

## 1. Latar Belakang & Tujuan

Guru BK membutuhkan alat bantu digital untuk memantau tingkat stress siswa secara berkala, terjadwal, dan terukur. Selama ini asesmen dilakukan manual sehingga sulit melacak histori individu, sulit menemukan siswa dengan kondisi tidak normal secara cepat, dan tindak lanjut (catatan, edukasi) tidak terdokumentasi dengan baik.

**Tujuan sistem:**
- Menstandardisasi pengukuran tingkat stress siswa menggunakan instrumen psikometri baku (PSS-10).
- Memberi guru BK visibilitas cepat terhadap siswa yang levelnya tidak normal, lengkap dengan histori antar tahun ajaran meski siswa berpindah kelas.
- Memberi siswa akses ke hasil, histori, catatan guru BK, serta materi edukasi penanganan stress.

## 2. Aktor & Role

| Role | Deskripsi |
|---|---|
| **Admin** | Mengelola akun Guru BK. Mewarisi seluruh hak akses Guru BK dan Siswa. |
| **Guru BK** | Mengelola data master (kelas, siswa, tahun ajaran), mengelola bank soal & jadwal asesmen, memberi catatan pada hasil siswa, mengelola konten literasi. Mewarisi hak akses Siswa. |
| **Siswa** | Mengerjakan asesmen sesuai jadwal, melihat statistik & histori dirinya sendiri, melihat catatan guru BK dan konten literasi. |

### Matriks Hak Akses (ringkas)

| Fitur | Admin | Guru BK | Siswa |
|---|:---:|:---:|:---:|
| CRUD akun Guru BK | ✅ | ❌ | ❌ |
| CRUD Tahun Ajaran | ✅ | ✅ | ❌ |
| CRUD Kelas | ✅ | ✅ | ❌ |
| CRUD Siswa (+ import Excel) | ✅ | ✅ | ❌ |
| Assign/kenaikan kelas siswa | ✅ | ✅ | ❌ |
| CRUD Bank Soal | ✅ | ✅ | ❌ |
| CRUD Assessment (rangkaian soal) | ✅ | ✅ | ❌ |
| CRUD Jadwal Assessment | ✅ | ✅ | ❌ |
| Lihat semua hasil siswa + dashboard sebaran level stress | ✅ | ✅ | ❌ |
| Beri catatan pada hasil asesmen siswa | ✅ | ✅ | ❌ |
| CRUD konten literasi (artikel/video) | ✅ | ✅ | ❌ |
| Kerjakan asesmen sesuai jadwal | ✅* | ✅* | ✅ |
| Lihat statistik & histori diri sendiri | ✅* | ✅* | ✅ |
| Lihat catatan, artikel, video | ✅* | ✅* | ✅ |

\* Admin & Guru BK secara teknis mewarisi UI siswa, namun fitur "kerjakan asesmen" hanya relevan bila akun tsb juga terdaftar sebagai siswa (umumnya tidak — disebut di sini demi kelengkapan pewarisan akses, bukan alur utama).

## 3. Alur Bisnis Utama (End-to-End)

1. Admin membuat akun Guru BK.
2. Guru BK membuat **Tahun Ajaran** aktif (mis. "2026/2027") dan **Kelas** (mis. "X IPA 1").
3. Guru BK membuat data **Siswa** (manual atau import Excel) — email login = `{nisn}@mindcheck.com`, password default = NISN, **wajib diganti siswa saat login pertama kali** — sekaligus menempatkan siswa ke kelas pada tahun ajaran berjalan.
4. Guru BK mengelola **Bank Soal PSS-10** (default ter-seed) dan membuat **Assessment** (mengelompokkan soal menjadi satu paket asesmen).
5. Guru BK membuat **Jadwal Assessment** (periode buka–tutup, target kelas/seluruh siswa) untuk tahun ajaran berjalan.
6. Siswa mengerjakan asesmen dalam jendela waktu jadwal → sistem menghitung skor & kategori otomatis (metodologi PSS-10).
7. Guru BK & siswa melihat hasil (skor, kategori, grafik).
8. Guru BK memberi **catatan** pada hasil asesmen siswa tertentu (khususnya siswa dengan kategori tidak normal).
9. Siswa melihat catatan dari guru BK, serta men-browse **artikel & video** literasi penanganan stress (library umum, tidak dipersonalisasi otomatis).
10. Di akhir tahun ajaran, Guru BK melakukan **kenaikan kelas massal**: siswa dipindahkan ke kelas baru pada tahun ajaran baru tanpa membuat data siswa baru — seluruh histori asesmen tahun-tahun sebelumnya tetap utuh dan terhubung ke siswa yang sama.

## 4. Metodologi Skoring — PSS-10 (Perceived Stress Scale)

Sesuai keputusan: menggunakan **instrumen standar PSS-10**, bukan bobot bebas per soal.

- 10 item pertanyaan baku (adaptasi Bahasa Indonesia), masing-masing dijawab skala Likert 5 poin: **0 = Tidak Pernah** s.d. **4 = Sangat Sering**.
- 4 item bersifat **reverse-scored** (item positif, nilai dibalik: 4-x) sesuai metodologi asli PSS-10.
- Skor total = penjumlahan seluruh item setelah reverse-scoring → rentang **0–40**.
- Kategori standar:
  | Rentang Skor | Kategori |
  |---|---|
  | 0–13 | Rendah / Normal |
  | 14–26 | Sedang |
  | 27–40 | Tinggi |

**Rekonsiliasi dengan kebutuhan "Guru BK CRUD soal/assessment":** bank soal & assessment tetap dapat dikelola (CRUD) oleh Guru BK untuk fleksibilitas operasional (redaksi bahasa, menambah soal pendamping/opsional, membuat beberapa paket asesmen berbeda periode), **namun** soal inti PSS-10 dan logika skoring (reverse-scoring, cut-off kategori) datang ter-seed sebagai default sistem dan direkomendasikan tidak diubah agar hasil tetap valid secara psikometri. **Keputusan ini telah dikonfirmasi (lihat §9.1).**

## 5. Model Data (Ringkasan Entitas)

- `users` — akun login (admin/guru_bk/siswa), role-based.
- `academic_years` — tahun ajaran (nama, tanggal mulai/selesai, status aktif).
- `school_classes` — master kelas (nama, tingkat) — reusable lintas tahun ajaran.
- `students` — profil siswa (relasi ke `users`, NISN, jenis kelamin, dll).
- `student_class_histories` — riwayat penempatan siswa: `student_id`, `academic_year_id`, `school_class_id`, status (`aktif` / `nonaktif`). `nonaktif` dipakai saat siswa lulus atau pindah sekolah — data & histori asesmen tetap tersimpan, hanya tidak muncul lagi di kelas aktif. **Kunci solusi kenaikan kelas** — histori asesmen tetap merujuk ke `student_id`, tidak terpengaruh perpindahan kelas.
- `questions` — bank soal PSS-10 (teks, urutan, flag `reverse_scored`, status aktif).
- `assessments` — paket asesmen (judul, deskripsi, daftar soal terkait).
- `assessment_schedules` — jadwal buka/tutup per tahun ajaran, target kelas/global.
- `assessment_results` — hasil per siswa per jadwal (skor total, kategori, waktu selesai).
- `assessment_answers` — jawaban per soal per hasil.
- `result_notes` — catatan guru BK pada `assessment_results` tertentu, terlihat oleh siswa pemilik hasil.
- `contents` — artikel & video literasi (judul, isi/deskripsi, tipe [artikel/video], url video jika ada, penulis, tanggal publish).

## 6. Fitur per Role (Detail)

### Siswa
- Dashboard statistik tingkat stress (grafik tren skor dari waktu ke waktu).
- Histori seluruh asesmen yang pernah dikerjakan.
- Detail hasil per asesmen (skor, kategori, catatan guru BK jika ada).
- Mengerjakan asesmen aktif sesuai jadwal (satu kali per jadwal, dalam window waktu berlaku).
- Melihat daftar artikel & video literasi penanganan stress.

### Guru BK (+ semua fitur Siswa)
- CRUD Tahun Ajaran.
- CRUD Kelas.
- CRUD Siswa, termasuk **import Excel** (template kolom: `NISN, Nama, Jenis Kelamin, Kelas` — email login digenerate otomatis `{nisn}@mindcheck.com`, password default = NISN).
- Kenaikan/assign kelas siswa antar tahun ajaran (bulk action, mempertahankan histori; siswa lulus ditandai `nonaktif`).
- CRUD Bank Soal (soal PSS-10 default + soal tambahan opsional).
- CRUD Assessment (paket soal).
- CRUD Jadwal Assessment (periode, target kelas). Satu siswa hanya bisa mengerjakan **1x per jadwal**.
- Dashboard rekap: sebaran kategori stress per kelas/sekolah, daftar siswa kategori "Tinggi"/tidak normal.
- **Ikon notifikasi (bell)** di header: daftar siswa dengan kategori "Tinggi" terbaru, read-only, dihitung otomatis dari `assessment_results` (bukan tabel notifikasi terpisah, bukan push/email — cukup dilihat saat guru BK membuka sistem).
- Lihat detail hasil semua siswa + beri **catatan** per hasil asesmen.
- CRUD konten literasi (artikel & video) — library umum, tidak dipersonalisasi otomatis.
- **Export PDF** laporan hasil per siswa (histori & detail asesmen).
- **Export Excel** rekap hasil asesmen untuk satu jadwal (seluruh siswa peserta jadwal tsb, skor & kategori masing-masing).

### Admin (+ semua fitur Guru BK)
- CRUD akun Guru BK.

## 7. Tech Stack

| Komponen | Pilihan |
|---|---|
| Framework | Laravel 12, PHP 8.2 |
| Auth | Laravel Breeze |
| Frontend/SPA | **Livewire + Volt** (`wire:navigate` untuk pengalaman SPA-like) — dipilih karena target hosting adalah **shared hosting**: tidak butuh proses Node/WebSocket persisten saat runtime, build asset (Vite) cukup sekali di lokal lalu upload, payload JS minim. |
| Styling | Tailwind CSS — **tanpa gradient**, palet warna solid/flat. |
| Database | MySQL — dev: `localhost:9898`, db `db_mindcheck`. Produksi mengikuti kredensial shared hosting (umumnya via socket/port default cPanel). |
| Chart/Diagram | ApexCharts (ringan, animasi halus, tanpa perlu gradient untuk tetap estetik) — dapat disesuaikan saat implementasi memakai skill `dataviz`. |
| Import & Export Excel | `maatwebsite/excel` (Laravel Excel) — dipakai untuk import data siswa dan export rekap hasil asesmen per jadwal. |
| Export PDF | `barryvdh/laravel-dompdf` — pure-PHP, tanpa dependency binary eksternal, aman di shared hosting. Dipakai untuk export laporan hasil per siswa. |
| Notifikasi | In-app bell icon, dihitung dinamis (query `assessment_results` kategori "Tinggi") — tanpa tabel `notifications` terpisah, tanpa WebSocket/push. |
| Standar UI/UX | Skill `ui-ux-pro-max` untuk implementasi awal, `impeccable` untuk audit/polish sebelum rilis tiap fitur besar. |

## 8. Non-Functional Requirements

- **Hosting**: harus berjalan di shared hosting (PHP-FPM/Apache, tanpa proses Node/queue worker/WebSocket persisten). Fitur realtime (jika ada) memakai polling, bukan broadcasting.
- **Keamanan**: password siswa default NISN — **wajib** ganti password saat login pertama kali (dipaksa, tidak bisa dilewati). Role-based middleware untuk membatasi akses per fitur sesuai matriks §2.
- **Skalabilitas data**: struktur `student_class_histories` mendukung multi-tahun-ajaran tanpa duplikasi data siswa.
- **Auditabilitas**: setiap `assessment_result` tercatat waktu pengerjaan; `result_notes` tercatat penulis & waktu.
- **Aksesibilitas & desain**: kontras warna cukup, tanpa gradient, chart harus tetap terbaca di mobile (siswa kemungkinan besar akses via HP).

## 9. Keputusan Desain (Final)

1. **Soal PSS-10 vs CRUD bebas** — ✅ Setuju. Bank soal core PSS-10 ter-seed & tidak diubah agar valid secara psikometri; Guru BK tetap punya UI CRUD untuk soal tambahan/paket lain.
2. **Password pertama kali** — ✅ Dipaksa. Siswa wajib ganti password saat login pertama kali (password default = NISN dianggap mudah ditebak).
3. **Email login siswa** — format tetap: `{nisn}@mindcheck.com`, digenerate otomatis oleh sistem (tidak perlu kolom email di template Excel).
4. **Pengerjaan asesmen** — 1x per jadwal, tidak bisa diulang.
5. **Notifikasi ke Guru BK** — cukup ditampilkan di sistem (ikon bell, in-app, read-only), **tidak perlu** push/email. Lihat detail teknis di §7.
6. **Kelulusan siswa** — `student_class_histories.status` diisi `nonaktif` saat siswa lulus/keluar; histori asesmen tetap tersimpan.

## 10. Di Luar Ruang Lingkup (Fase 1)

- Notifikasi realtime/push (WebSocket/email) — cukup in-app bell read-only (§9.5); WebSocket/push eksplisit di luar ruang lingkup karena tidak feasible di shared hosting tanpa layanan pihak ketiga (Pusher dkk.).
- Personalisasi otomatis artikel/video berdasarkan level stress — tetap library umum (§9 lama, dikonfirmasi ulang).

---

**Langkah selanjutnya:** setelah PRD ini dikonfirmasi/direvisi, saya akan susun rencana implementasi (struktur migration, seeder PSS-10, dan urutan pembangunan fitur per role).
