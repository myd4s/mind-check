<?php

use App\Livewire\GuruBk\AcademicYearManagement;
use App\Livewire\GuruBk\AssessmentManagement;
use App\Livewire\GuruBk\AssessmentResultManagement;
use App\Livewire\GuruBk\AssessmentScheduleManagement;
use App\Livewire\GuruBk\ClassPromotion;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\StudentReportPdfController;
use App\Livewire\Admin\GuruBkAccountManagement;
use App\Livewire\GuruBk\ContentManagement;
use App\Livewire\GuruBk\QuestionManagement;
use App\Livewire\GuruBk\SchoolClassManagement;
use App\Livewire\GuruBk\StudentManagement;
use App\Livewire\GuruBk\StudentParticipation;
use App\Livewire\Shared\AssessmentResultDetail;
use App\Livewire\Siswa\AssessmentHistory;
use App\Livewire\Siswa\AssessmentWizard;
use App\Livewire\Siswa\AvailableAssessments;
use App\Livewire\Siswa\ContentDetail;
use App\Livewire\Siswa\ContentLibrary;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingPageController::class);

// Tidak ada alur verifikasi email (PRD §2) — seluruh akun diprovisioning
// tertutup oleh Admin/Guru BK, bukan self-registrasi, sehingga middleware
// 'verified' tidak relevan di sistem ini.
Route::view('dashboard', 'dashboard')
    ->middleware(['auth'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth', 'role:guru_bk'])->prefix('guru-bk')->name('guru-bk.')->group(function () {
    Route::get('tahun-ajaran', AcademicYearManagement::class)->name('academic-years');
    Route::get('kelas', SchoolClassManagement::class)->name('school-classes');
    Route::get('siswa', StudentManagement::class)->name('students');
    Route::get('kenaikan-kelas', ClassPromotion::class)->name('class-promotion');
    Route::get('soal', QuestionManagement::class)->name('questions');
    Route::get('assessment', AssessmentManagement::class)->name('assessments');
    Route::get('jadwal-assessment', AssessmentScheduleManagement::class)->name('assessment-schedules');
    Route::get('partisipasi-asesmen', StudentParticipation::class)->name('assessment-participation');
    Route::get('hasil-assessment', AssessmentResultManagement::class)->name('results');
    Route::get('konten', ContentManagement::class)->name('contents');
});

Route::middleware(['auth', 'role:siswa'])->prefix('asesmen')->name('siswa.')->group(function () {
    Route::get('/', AvailableAssessments::class)->name('available-assessments');
    Route::get('riwayat', AssessmentHistory::class)->name('history');
    Route::get('hasil/{result}', AssessmentResultDetail::class)->name('result-detail');
    Route::get('laporan/{student}', StudentReportPdfController::class)->name('report-pdf');
    Route::get('{schedule}/kerjakan', AssessmentWizard::class)->name('assessment-wizard');
});

Route::middleware(['auth', 'role:siswa'])->prefix('literasi')->name('siswa.')->group(function () {
    Route::get('/', ContentLibrary::class)->name('content-library');
    Route::get('{content}', ContentDetail::class)->name('content-detail');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('guru-bk', GuruBkAccountManagement::class)->name('guru-bk-accounts');
});

require __DIR__.'/auth.php';
