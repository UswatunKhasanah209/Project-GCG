<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\AdminPenilaianController;
use App\Http\Controllers\AspekController;
use App\Http\Controllers\AspekGcgController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | PROFILE
    |--------------------------------------------------------------------------
    | Alur:
    | /profile         = halaman profil utama
    | /profile/account = halaman akun
    | /profile/edit    = halaman edit profil
    */
    Route::get('/profile', [ProfileController::class, 'index'])
        ->name('profile.index');

    Route::get('/profile/account', [ProfileController::class, 'account'])
        ->name('profile.account');

    Route::get('/profile/edit', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::get('/profile/history', [ProfileController::class, 'history'])
        ->name('profile.history');

    Route::get('/profile/info', [ProfileController::class, 'info'])
        ->name('profile.info');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::post('/profile/logout', [ProfileController::class, 'logout'])
        ->name('profile.logout');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | ASPEK LAMA
    |--------------------------------------------------------------------------
    */
    Route::get('/aspects', [AspekController::class, 'index'])
        ->name('aspects.index');

    Route::get('/admin/aspects/divisions/{division}', [AspekController::class, 'divisionDetail'])
        ->name('aspects.admin.division');

    Route::get('/user/aspects', [AspekController::class, 'userIndex'])
        ->name('aspects.user');

    /*
    |--------------------------------------------------------------------------
    | ASPEK GCG
    |--------------------------------------------------------------------------
    */
    Route::prefix('aspek-gcg')
        ->name('aspek.gcg.')
        ->controller(AspekGcgController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{aspek}', 'show')->name('show');
            Route::post('/fuk-status', 'updateFukStatus')->name('fukStatus.update');
        });

    /*
    |--------------------------------------------------------------------------
    | PENILAIAN
    |--------------------------------------------------------------------------
    */
    Route::get('/penilaian', [PenilaianController::class, 'index'])
        ->name('penilaian.index');

    Route::get('/penilaian/review', [PenilaianController::class, 'reviewIndex'])
        ->name('penilaian.review');

    Route::post('/penilaian/review/{document}/update-status', [PenilaianController::class, 'updateStatus'])
        ->name('penilaian.review.update');

    Route::prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/penilaian', [AdminPenilaianController::class, 'index'])
                ->name('penilaian.index');

            Route::get('/penilaian/aspek/{aspek}', [AdminPenilaianController::class, 'showAspek'])
                ->name('penilaian.aspek');

            Route::get('/penilaian/fuk/{fuk}', [AdminPenilaianController::class, 'showFukForm'])
                ->name('penilaian.fuk.form');

            Route::post('/penilaian/fuk/{fuk}', [AdminPenilaianController::class, 'saveFukReview'])
                ->name('penilaian.fuk.save');
        });

    /*
    |--------------------------------------------------------------------------
    | LIBRARY
    |--------------------------------------------------------------------------
    */
    Route::prefix('library')
        ->name('library.')
        ->controller(LibraryController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');

            Route::get('/upload', 'uploadPage')->name('uploadPage');
            Route::post('/upload', 'upload')->name('upload');

            Route::post('/documents/{document}/replace', 'replaceDocument')
                ->name('documents.replace');

            Route::post('/documents/{document}/status', 'updateStatus')
                ->name('documents.updateStatus');

            Route::get('/download', 'downloadIndex')
                ->name('downloadIndex');

            Route::get('/download/worksheet', 'downloadWorksheet')
                ->name('downloadWorksheet');

            Route::get('/download/report', 'downloadReport')
                ->name('downloadReport');

            Route::get('/download/excel/{type}', 'downloadExcel')
                ->name('downloadExcel');

            Route::get('/download/pdf/{type}', 'downloadPDF')
                ->name('downloadPDF');

            Route::get('/indikators/{aspek}', 'indikators')
                ->name('indikators');

            Route::get('/parameters/{indikator}', 'parameters')
                ->name('parameters');

            Route::get('/fuks/{parameter}', 'fuksByParameter')
                ->name('fuks');

            Route::get('/fuk-children/{fuk}', 'fukChildren')
                ->name('fukChildren');

            Route::get('/documents/{document}/download', 'downloadDocument')
                ->name('documents.download');

            Route::delete('/documents/{document}', 'destroyDocument')
                ->name('documents.destroy');

            Route::patch('/documents/{document}/rename', 'renameDocument')
                ->name('documents.rename');
        });
});

require __DIR__ . '/auth.php';