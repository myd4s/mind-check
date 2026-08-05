# ISSUES — MindCheck

Breakdown implementasi berdasarkan [PRD.md](PRD.md). Setiap issue didesain agar bisa dieksekusi satu per satu (berurutan sesuai nomor & dependency), dengan scope yang jelas dan Definition of Done (DoD) sebagai kriteria selesai.

Cara pakai: kerjakan satu issue, centang DoD-nya, baru lanjut issue berikutnya. Issue dalam satu milestone boleh dikerjakan berurutan; lintas milestone sebaiknya ikuti urutan nomor karena ada dependency data (mis. Assessment butuh Question Bank).

---

## Milestone 0 — Project Foundation

### #1 Setup project Laravel 12 + Breeze (Livewire/Volt) + Tailwind
**Deps:** —
**Scope:**
- Init Laravel 12 project (PHP 8.2), konfigurasi `.env` untuk MySQL `localhost:9898` / db `db_mindcheck`.
- Install Laravel Breeze stack Livewire (`php artisan breeze:install livewire`), aktifkan Volt.
- Konfigurasi Tailwind: hapus/nonaktifkan utility gradient dari palet default, tentukan palet warna solid dasar (primary/secondary/accent/success/warning/danger) sesuai standar `ui-ux-pro-max`.
- Setup `wire:navigate` di layout utama untuk pengalaman SPA-like.
- Install package: `barryvdh/laravel-dompdf`, `maatwebsite/excel`.
**DoD:**
- [x] `php artisan serve` jalan, halaman login Breeze tampil ter-styling Tailwind tanpa gradient.
- [x] Migrasi default Breeze berhasil jalan ke `db_mindcheck`.
- [x] Package dompdf & excel ter-install dan bisa di-resolve (`composer show`).

### #2 Role & access control foundation
**Deps:** #1
**Scope:**
- Tambah kolom `role` (enum: `admin`, `guru_bk`, `siswa`) di tabel `users` (migration).
- Buat `UserRole` enum (PHP 8.1 backed enum).
- Middleware `EnsureUserHasRole` (mendukung hierarki: admin ⊇ guru_bk ⊇ siswa sesuai matriks PRD §2).
- Base layout per role: shell navigasi berbeda untuk Admin/Guru BK vs Siswa (sidebar/menu items sesuai matriks akses).
- Seeder akun awal: 1 admin, 1 guru_bk, 1 siswa dummy untuk testing manual.
**DoD:**
- [x] Login sebagai masing-masing role mengarah ke dashboard/menu yang sesuai matriks PRD §2.
- [x] Akses route role lain menghasilkan 403.
- [x] Seeder `CoreAccountsSeeder` jalan tanpa error.

> Catatan implementasi: registrasi publik (`/register`) dinonaktifkan karena bertentangan dengan model provisioning akun tertutup di PRD §2 (seluruh akun dibuat oleh Admin/Guru BK, bukan self-service).

---

## Milestone 1 — Master Data

### #3 CRUD Tahun Ajaran (Academic Year)
**Deps:** #2
**Scope:** Migration `academic_years` (nama, tanggal mulai, tanggal selesai, `is_active`), Livewire/Volt component CRUD (list, create, edit, delete), hanya 1 tahun ajaran boleh `is_active` pada satu waktu (auto-nonaktifkan yang lain saat set aktif baru).
**DoD:**
- [x] Guru BK & Admin bisa CRUD tahun ajaran.
- [x] Validasi: tidak ada 2 tahun ajaran aktif bersamaan.
- [x] Siswa tidak punya akses ke halaman ini (403).

### #4 CRUD Kelas (School Class)
**Deps:** #2
**Scope:** Migration `school_classes` (nama, tingkat), Livewire/Volt CRUD. Kelas bersifat master data reusable lintas tahun ajaran (sesuai PRD §5).
**DoD:**
- [x] Guru BK & Admin bisa CRUD kelas.
- [ ] Tidak bisa hapus kelas yang masih dipakai di `student_class_histories` aktif (soft guard/validasi) — **ditunda ke #5**, karena tabel `student_class_histories` baru dibuat di issue tersebut (dependency terbalik dari urutan asli).

### #5 CRUD Siswa (manual) + auto-provision akun login
**Deps:** #3, #4
**Scope:**
- Migration `students` (relasi ke `users`, NISN, jenis kelamin, dll) + `student_class_histories` (student_id, academic_year_id, school_class_id, status `aktif`/`nonaktif`).
- Form create siswa: input NISN, nama, jenis kelamin, pilih kelas (pada tahun ajaran aktif) → otomatis buat `User` (email `{nisn}@mindcheck.com`, password default = NISN, role `siswa`) + record `student_class_histories` status `aktif`.
- Flag `must_change_password` pada `users` (dipakai issue #6).
- List & edit & nonaktifkan siswa.
**DoD:**
- [x] Create siswa menghasilkan akun login yang valid (bisa login dengan email & NISN).
- [x] Edit data siswa tidak mengubah histori kelas tahun ajaran sebelumnya.
- [x] Nonaktifkan siswa mengubah status `student_class_histories` terkini jadi `nonaktif`, histori lama tetap ada.

> Catatan implementasi: guard hapus kelas dari #4 (tidak bisa hapus kelas dengan siswa aktif) diselesaikan di sini karena `student_class_histories` baru ada sekarang. Kolom `must_change_password` di `users` juga ditambahkan di sini (bukan di #6) karena harus di-set `true` sejak akun siswa pertama kali dibuat.

### #6 Paksa ganti password saat login pertama
**Deps:** #5
**Scope:** Middleware/redirect: jika `users.must_change_password = true`, paksa ke halaman ganti password sebelum bisa akses fitur lain. Set `false` setelah berhasil ganti.
**DoD:**
- [x] Login pertama siswa baru selalu diarahkan ke form ganti password, tidak bisa di-skip via URL langsung.
- [x] Setelah ganti password, redirect normal ke dashboard sesuai role.

### #7 Import Siswa via Excel
**Deps:** #5
**Scope:**
- Template Excel unduhan: kolom `NISN, Nama, Jenis Kelamin, Kelas`.
- Import handler (`maatwebsite/excel`): validasi baris (NISN unik, kelas harus ada di master), buat akun + `student_class_histories` sama seperti create manual (§#5).
- Laporan hasil import (baris sukses/gagal beserta alasan).
**DoD:**
- [x] Import file valid membuat N akun siswa sekaligus dengan email/password sesuai konvensi.
- [x] Baris invalid (NISN duplikat, kelas tidak ditemukan) dilaporkan tanpa menggagalkan baris lain yang valid.

### #8 Kenaikan kelas massal (antar tahun ajaran)
**Deps:** #5, #3
**Scope:** UI bulk-assign: pilih tahun ajaran baru + tahun ajaran lama, mapping kelas lama → kelas baru per siswa (atau per rombongan), submit membuat batch `student_class_histories` baru tanpa mengubah data siswa/histori lama. Opsi tandai siswa tertentu sebagai `nonaktif` (lulus) alih-alih naik kelas.
**DoD:**
- [x] Setelah kenaikan kelas, siswa punya 2 baris `student_class_histories` (tahun lama status apa adanya, tahun baru status `aktif`) tanpa duplikasi data siswa.
- [x] Histori assessment lama tetap terhubung & terlihat utuh di profil siswa yang sama.

---

## Milestone 2 — Assessment Core (PSS-10)

### #9 Seeder Bank Soal PSS-10 + CRUD Soal
**Deps:** #2
**Scope:** Migration `questions` (teks, urutan, `reverse_scored`, `is_active`). Seeder 10 item PSS-10 (adaptasi Bahasa Indonesia) dengan flag reverse-scored sesuai metodologi asli (PRD §4). CRUD soal untuk Guru BK (tambah soal pendamping/opsional; soal core PSS-10 tetap bisa diedit redaksinya tapi flag `reverse_scored` & keterkaitan skor dijaga integritasnya).
**DoD:**
- [x] Seeder menghasilkan 10 soal PSS-10 dengan flag reverse-scored yang benar.
- [x] Guru BK bisa CRUD soal tambahan tanpa merusak soal core.

> Catatan implementasi: nav digrupkan jadi dropdown "Data Master" & "Asesmen" karena item makin banyak (5+).

### #10 CRUD Assessment (paket soal)
**Deps:** #9
**Scope:** Migration `assessments` + pivot `assessment_question` (assessment_id, question_id, urutan). CRUD paket asesmen oleh Guru BK, default seed 1 assessment "Asesmen Stress PSS-10" berisi 10 soal core.
**DoD:**
- [x] Guru BK bisa membuat paket asesmen baru dari kombinasi soal di bank.
- [x] Assessment default PSS-10 tersedia langsung setelah seeding.

### #11 CRUD Jadwal Assessment
**Deps:** #10, #3, #4
**Scope:** Migration `assessment_schedules` (assessment_id, academic_year_id, tanggal mulai, tanggal selesai, target: semua kelas / kelas tertentu, `is_active`), pivot target kelas jika perlu. CRUD oleh Guru BK.
**DoD:**
- [x] Guru BK bisa membuat jadwal dengan window waktu & target kelas spesifik.
- [x] Jadwal di luar window waktu tidak muncul sebagai "bisa dikerjakan" di sisi siswa (logika `isOpenNow()` teruji; penerapan penuh di sisi siswa menyusul di #12).

### #12 Wizard pengerjaan Assessment (Siswa) + scoring engine
**Deps:** #11
**Scope:**
- UI wizard Livewire: siswa menjawab tiap soal skala Likert 5 (0–4), progress indicator, submit di akhir.
- Guard: siswa hanya bisa mengerjakan 1x per jadwal (cek `assessment_results` existing).
- Scoring engine: hitung skor total dengan reverse-scoring sesuai flag soal, mapping ke kategori (Rendah/Sedang/Tinggi) sesuai cut-off PRD §4.
- Simpan `assessment_results` + `assessment_answers`.
**DoD:**
- [x] Siswa menyelesaikan wizard menghasilkan skor & kategori yang benar (uji dengan kasus known-answer manual — lihat `Pss10ScoringServiceTest` & `AssessmentWizardTest`).
- [x] Percobaan mengerjakan ulang jadwal yang sama diblokir dengan pesan jelas.

> Catatan implementasi: dibuat juga halaman "Asesmen Tersedia" (`/asesmen`) sebagai entry point ke wizard — di luar scope literal issue ini tapi diperlukan karena tanpa halaman ini wizard tidak punya jalan masuk. Detail hasil lengkap (skor historis, catatan) menyusul di #13.

### #13 Detail & Histori Hasil Assessment
**Deps:** #12
**Scope:** Halaman detail hasil (skor, kategori, jawaban per soal) untuk siswa (miliknya sendiri) dan guru BK (semua siswa). Halaman histori/list seluruh hasil per siswa.
**DoD:**
- [x] Siswa hanya bisa melihat hasil miliknya sendiri.
- [x] Guru BK bisa melihat & memfilter hasil semua siswa (per kelas/jadwal/kategori).

> Catatan implementasi: dibuat komponen `x-category-badge` reusable untuk badge kategori (dipakai di histori siswa, detail hasil, dan list guru BK) agar konsisten & tidak duplikasi logika warna.

---

## Milestone 3 — Dashboard, Catatan & Notifikasi

### #14 Dashboard Siswa (statistik & tren)
**Deps:** #13
**Scope:** Grafik tren skor dari waktu ke waktu (ApexCharts), ringkasan kategori terakhir, shortcut ke asesmen aktif jika ada.
**DoD:**
- [x] Grafik menampilkan histori skor siswa yang login, update otomatis setelah asesmen baru.
- [x] Tampilan tetap terbaca di mobile (breakpoint Tailwind).

> **Bug ditemukan & diperbaiki saat verifikasi browser end-to-end (bukan dari scope #14 itu sendiri):**
> 1. Middleware `EnsureUserHasChangedPassword` (dari #6) memblokir request AJAX Livewire (`POST /livewire/update`) untuk aksi `updatePassword` itu sendiri — bikin deadlock permanen. `Livewire::test()` tidak menangkap ini karena melewati pipeline HTTP/middleware. Fix: lewatkan request dengan header `X-Livewire`. Regresi: `EnsureUserHasChangedPasswordMiddlewareTest`.
> 2. `Student::currentClassHistory()` (dari #5) pakai `latestOfMany()` yang menghitung `MAX(id)` SEBELUM filter tahun aktif diterapkan — kalau baris ID tertinggi (hasil kenaikan kelas ke tahun depan) bukan tahun aktif, seluruh relasi mengembalikan `null` walau ada baris valid yang lebih lama. Fix: ganti ke `orderByDesc('id')` dengan filter sudah menyatu di query utama. Regresi: `StudentModelTest`.
>
> Chart pakai ApexCharts dengan zona warna latar (rendah/sedang/tinggi) mengikuti status palette PRD §4, divalidasi via skill `dataviz` (status color dikecualikan dari cek CVD kategorikal ketat karena selalu berpasangan dengan label teks).

### #15 Dashboard Guru BK (rekap & sebaran kategori)
**Deps:** #13
**Scope:** Rekap sebaran kategori stress per kelas/sekolah (chart), daftar siswa kategori "Tinggi" terbaru, filter per kelas/tahun ajaran.
**DoD:**
- [x] Dashboard menampilkan data akurat sesuai hasil assessment terbaru per siswa.
- [x] Filter kelas/tahun ajaran berfungsi tanpa reload penuh (Livewire).

> Catatan implementasi: Admin sekarang juga melihat dashboard Guru BK yang sama (bukan placeholder terpisah) karena PRD §2 menyatakan Admin mewarisi seluruh akses Guru BK.

### #16 Catatan Guru BK pada hasil asesmen
**Deps:** #13
**Scope:** Migration `result_notes` (assessment_result_id, guru_bk_id, isi, timestamps). Form tambah/edit catatan dari halaman detail hasil (guru BK), tampil di halaman detail hasil siswa (read-only untuk siswa).
**DoD:**
- [x] Guru BK bisa menambah/mengedit catatan pada hasil asesmen tertentu.
- [x] Siswa pemilik hasil melihat catatan tsb di halaman detail hasilnya, siswa lain tidak bisa.

### #17 Notifikasi bell (Guru BK)
**Deps:** #13
**Scope:** Komponen dropdown bell di header Guru BK, query dinamis `assessment_results` kategori "Tinggi" terbaru (tanpa tabel notifikasi terpisah sesuai PRD §7), badge counter, klik item → ke detail hasil terkait.
**DoD:**
- [x] Bell menampilkan daftar siswa kategori "Tinggi" terbaru secara real (via polling/refresh saat navigasi, bukan WebSocket).
- [x] Klik item mengarahkan ke detail hasil yang benar.

> Catatan implementasi: bell diimplementasikan sebagai computed property (`highCategoryNotifications`) di komponen Volt `layout.navigation` (dipakai di semua halaman), query `AssessmentResult::where('category', 'tinggi')` diurutkan `completed_at` terbaru & dibatasi 8 item — tanpa filter tahun ajaran/kelas (beda dari `Dashboard::highCategoryStudents` yang scoped ke filter dashboard), karena notifikasi dimaksudkan sebagai sinyal global lintas waktu. Recompute otomatis tiap request/navigasi karena computed property Livewire tidak persist antar request, sehingga "polling" DoD terpenuhi tanpa kerja tambahan. Versi ringkas juga ditambahkan ke menu hamburger mobile agar konsisten dengan versi desktop.

---

## Milestone 4 — Konten Literasi

### #18 CRUD Artikel & Video + listing siswa
**Deps:** #2
**Scope:** Migration `contents` (judul, isi/deskripsi, tipe [artikel/video], url video, penulis, tanggal publish). CRUD oleh Guru BK/Admin. Halaman listing & detail untuk siswa (library umum, tanpa personalisasi otomatis sesuai PRD §9).
**DoD:**
- [x] Guru BK bisa CRUD artikel & video (termasuk embed URL video).
- [x] Siswa bisa browse & baca/tonton konten tanpa batasan personalisasi.

> Catatan implementasi: `Content::embedUrl()` mengonversi URL YouTube (watch/share/shorts) ke bentuk `/embed/` untuk `<iframe>`; URL non-YouTube dilewatkan apa adanya. Listing siswa (`siswa.content-library`) & detail (`siswa.content-detail`) dipasang di prefix `literasi` terpisah dari `asesmen` agar tidak bentrok nama; keduanya dibuka lewat `role:siswa` sehingga guru_bk/admin ikut bisa melihat (hierarki akses PRD §2), sama seperti pola "Asesmen Saya" yang sudah ada di nav. Nav link "Literasi" ditaruh di level atas (tidak di-gate role) mengikuti pola tersebut; link CRUD "Konten Literasi" khusus guru_bk digabung ke dropdown "Asesmen" yang sudah ada agar tidak menambah grup dropdown baru.

---

## Milestone 5 — Reporting

### #19 Export PDF laporan hasil per siswa
**Deps:** #13, #16
**Scope:** Generate PDF (dompdf) berisi profil siswa, histori skor, grafik sederhana (jika feasible di dompdf) atau tabel, catatan guru BK. Tombol export di halaman detail/histori siswa (akses guru BK & siswa yang bersangkutan).
**DoD:**
- [x] PDF ter-generate dengan data akurat & terbaca rapi (uji cetak/preview).
- [x] Akses export dibatasi sesuai kepemilikan data (siswa hanya bisa export miliknya sendiri).

> Catatan implementasi: `StudentReportPdfController` (plain controller, bukan Livewire) di-mount lewat route `siswa.report-pdf` (`GET /asesmen/laporan/{student}`), dengan guard kepemilikan yang sama persis dengan `AssessmentResultDetail::mount()` (guru_bk+ bebas akses, siswa hanya boleh export data miliknya sendiri → 403 jika bukan). PDF berisi profil siswa, seluruh histori skor (bukan cuma 1 hasil) dalam bentuk tabel (grafik tidak feasible di dompdf tanpa JS/canvas, sesuai opsi fallback di scope), dan seluruh catatan Guru BK per hasil yang ada catatannya. Tombol "Export PDF" ditaruh di halaman Detail Hasil (`assessment-result-detail.blade.php`, dipakai siswa & guru_bk) dan halaman Histori Asesmen siswa (`assessment-history.blade.php`) — export dari kedua halaman selalu menghasilkan laporan LENGKAP siswa tsb (bukan cuma 1 hasil), sesuai scope "laporan hasil per siswa". Diverifikasi manual: response `Content-Type: application/pdf`, `Content-Disposition: attachment`, PDF ter-generate valid (3.3KB, non-kosong).

### #20 Export Excel rekap hasil per jadwal
**Deps:** #13
**Scope:** Export Excel (maatwebsite/excel) dari halaman jadwal assessment: daftar siswa peserta jadwal tsb beserta skor & kategori masing-masing. Akses Guru BK/Admin.
**DoD:**
- [x] File Excel ter-download berisi seluruh siswa peserta jadwal (termasuk yang belum mengerjakan, ditandai jelas).
- [x] Kolom & data sesuai (NISN, Nama, Kelas, Skor, Kategori, Waktu Selesai).

> Catatan implementasi: `AssessmentScheduleResultsExport` (maatwebsite/excel `FromCollection`) menentukan peserta dari `student_class_histories` pada `academic_year_id` jadwal tsb (difilter ke `targetClasses` jika `target_type = specific`), bukan dari `assessment_results` — supaya siswa yang belum mengerjakan tetap ikut ter-daftar dengan kategori "Belum Mengerjakan" & skor "—", sesuai DoD. Tombol "Export Excel" ditambahkan ke baris tabel `guru-bk.assessment-schedules` (aksi ke-3 di kolom Aksi), memicu `Excel::download()` langsung dari method action Livewire (pola sama seperti `downloadTemplate()` di `StudentManagement` dari #7). Menambahkan tombol ke-3 membuat tabel jadwal melebihi lebar container dan tombolnya ter-clip oleh `overflow-hidden` — diperbaiki dengan mengganti ke `overflow-x-auto` supaya bisa di-scroll horizontal alih-alih terpotong.

---

## Milestone 6 — Admin

### #21 CRUD Akun Guru BK (Admin)
**Deps:** #2
**Scope:** Halaman khusus Admin untuk create/edit/nonaktifkan akun Guru BK.
**DoD:**
- [x] Hanya Admin yang bisa akses halaman ini (403 untuk role lain).
- [x] Akun Guru BK baru bisa langsung login sesuai role.

> Catatan implementasi: menambahkan kolom `users.is_active` (boolean, default `true`, migration baru) karena sebelumnya tidak ada mekanisme "nonaktifkan" akun sama sekali di skema — Guru BK tidak punya konsep histori kelas seperti siswa untuk merepresentasikan status aktif/nonaktif. "Nonaktifkan" bersifat toggle (bukan hapus permanen) supaya relasi FK yang sudah ada (mis. `result_notes.guru_bk_id`) tetap utuh. `LoginForm::authenticate()` diperluas: setelah `Auth::attempt` sukses, cek `is_active` — jika `false`, langsung logout & lempar pesan validasi "Akun ini telah dinonaktifkan." (akun siswa/admin lain tidak terdampak karena defaultnya selalu `true`). Halaman di `App\Livewire\Admin\GuruBkAccountManagement`, route `role:admin` (bukan `role:guru_bk`, supaya guru_bk sendiri TIDAK bisa akses meski hierarki §2 biasanya guru_bk ⊆ admin — di sini arahnya terbalik, admin-only, sesuai scope "khusus Admin"). Nav link "Akun Guru BK" hanya muncul untuk role Admin persis (`hasRoleAtLeast(Admin)`, bukan `GuruBk`, karena level Admin adalah level tertinggi jadi otomatis eksklusif ke Admin).

---

## Milestone 7 — Polish & Audit

### #22 Audit UI/UX menyeluruh (skill `impeccable`)
**Deps:** semua milestone 1–6 selesai
**Scope:** Jalankan audit skill `impeccable` per halaman/role, perbaiki temuan (konsistensi spacing, kontras warna tanpa gradient, responsivitas mobile terutama untuk siswa, aksesibilitas form).
**DoD:**
- [x] Semua halaman utama (dashboard 3 role, wizard asesmen, CRUD master data) lolos audit tanpa temuan kritikal.

> Catatan implementasi: audit via skill `impeccable` menemukan & memperbaiki temuan sistemik berikut (tanpa mengubah struktur data/logic bisnis):
> 1. **[P1 Responsive]** 12 tabel CRUD (`overflow-hidden` pada container) meng-clip aksi baris (tombol Edit/Hapus/dst.) alih-alih scroll horizontal saat lebar konten melebihi container — terjadi di semua halaman manajemen guru_bk/admin + histori asesmen siswa. Fix: ganti ke `overflow-x-auto` di 12 file (dashboard, academic-year, school-class, student, content, question ×2, assessment, assessment-result, assessment-schedule, class-promotion, admin guru-bk-accounts, siswa assessment-history). Diverifikasi live di viewport mobile (375px) — tabel sekarang scroll dengan scrollbar terlihat, bukan terpotong.
> 2. **[P2 A11y]** Tombol hamburger mobile (Breeze default) tidak punya accessible name maupun `aria-expanded`. Fix: tambah `aria-label` & `:aria-expanded="open"`.
> 3. **[P2 Contrast]** `text-gray-400` (~2.8:1 terhadap putih, gagal WCAG AA 4.5:1) dipakai untuk teks berisi informasi (timestamp catatan, tanggal publish konten, dash "belum ada data") di 6 halaman — bukan sekadar elemen dekoratif. Fix: naikkan ke `text-gray-500` (~4.6:1, lolos AA), token yang sudah dipakai konsisten sebagai warna teks sekunder di seluruh aplikasi.
> 4. Wizard asesmen siswa (`assessment-wizard.blade.php`), dashboard 3 role, dan halaman Literasi sudah diverifikasi baik di viewport mobile (375px) — tidak ada temuan kritikal (touch target tombol jawaban wizard sudah ≥44px, layout stack rapi).
> 5. `php artisan test` tetap hijau (142 test) setelah semua perbaikan — perubahan murni CSS/markup, tidak menyentuh logic.

### #23 Testing pass (feature tests per modul)
**Deps:** setiap issue terkait
**Scope:** Feature test Pest/PHPUnit minimal untuk: role access matrix, scoring engine PSS-10 (kasus reverse-score), guard 1x per jadwal, kenaikan kelas (histori tidak hilang), export PDF/Excel tidak error.
**DoD:**
- [x] `php artisan test` hijau semua untuk modul-modul di atas.

> Catatan implementasi: cakupan test untuk tiap modul di scope sudah ada, tersebar di seluruh proses implementasi #1-#22 (bukan file terpisah baru untuk issue ini):
> - Role access matrix → `RoleAccessTest` + `test_siswa_cannot_access_page`/`test_guru_bk_cannot_access_page` di tiap `*ManagementTest`.
> - Scoring engine PSS-10 (reverse-score) → `Pss10ScoringServiceTest` (`test_reverse_item_score_is_inverted`, dll).
> - Guard 1x per jadwal → `AssessmentWizardTest::test_cannot_submit_twice_for_the_same_schedule`.
> - Kenaikan kelas (histori tidak hilang) → `ClassPromotionTest::test_promotes_students_to_mapped_class_in_target_year_without_losing_old_history`.
> - Export PDF/Excel tidak error → `StudentReportPdfTest` (assert `Content-Type: application/pdf`) & `AssessmentScheduleResultsExportTest` (assert isi export + `Excel::assertDownloaded`).
> `php artisan test`: **142 passed, 0 failed** (314 assertions).

---

**Catatan eksekusi:** kerjakan berurutan per nomor issue dalam satu milestone dulu sebelum lanjut milestone berikutnya, karena tiap issue mengasumsikan data/struktur dari issue dependency-nya sudah ada.
