<?php

use App\Exports\StudentImportTemplateExport;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\Counselor\DashboardController as CounselorDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\ResultController;
use App\Livewire\Admin\StudentManagement;
use App\Livewire\Questionnaire\Wizard;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

Route::view('/', 'welcome');

Route::get('dashboard', DashboardController::class)
    ->middleware(['auth'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth', 'role:student'])->prefix('siswa')->name('student.')->group(function () {
    Route::get('dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::get('kuesioner', Wizard::class)->name('questionnaire');
    Route::get('hasil/{assessment}', [ResultController::class, 'show'])->name('result');
    Route::get('hasil/{assessment}/pdf', [ResultController::class, 'downloadPdf'])->name('result.pdf');
});

Route::middleware(['auth', 'role:counselor'])->prefix('konselor')->name('counselor.')->group(function () {
    Route::get('dashboard', [CounselorDashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('siswa', StudentManagement::class)->name('students');
    Route::get('siswa/template', fn () => Excel::download(new StudentImportTemplateExport, 'template-import-siswa.xlsx'))
        ->name('students.template');
});

Route::middleware(['auth', 'role:counselor,admin'])->prefix('hasil-siswa')->name('assessment.')->group(function () {
    Route::get('{assessment}', [AssessmentController::class, 'show'])->name('show');
    Route::post('{assessment}/follow-up', [AssessmentController::class, 'storeFollowUp'])->name('follow-up.store');
    Route::get('{assessment}/pdf', [AssessmentController::class, 'downloadPdf'])->name('pdf');
});

require __DIR__.'/auth.php';
