<?php

use App\Http\Controllers\Admin\UserApprovalController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DocumentCirculationController;
use App\Http\Controllers\RruController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/equipment-relocation');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/user-approvals', [UserApprovalController::class, 'index'])->name('user-approvals.index');
        Route::post('/user-approvals/{user}/approve', [UserApprovalController::class, 'approve'])->name('user-approvals.approve');
        Route::post('/user-approvals/{user}/reject', [UserApprovalController::class, 'reject'])->name('user-approvals.reject');
    });

    Route::get('/equipment-relocation', [RruController::class, 'index'])->name('equipment-relocation.index');
    Route::get('/equipment-relocation/relocation-data', [RruController::class, 'relocationData'])->name('equipment-relocation.relocation-data');
    Route::post('/equipment-relocation/relocation-data', [RruController::class, 'saveRelocation'])->name('equipment-relocation.relocation-data.store');
    Route::delete('/equipment-relocation/relocation-data', [RruController::class, 'deleteRelocation'])->name('equipment-relocation.relocation-data.destroy');
    Route::redirect('/rru', '/equipment-relocation')->name('rru.index');

    Route::prefix('po-monitoring/presales')->name('presales.')->group(function () {
        Route::get('/', [DocumentCirculationController::class, 'index'])->name('index');
        Route::get('/create', [DocumentCirculationController::class, 'create'])->name('create');
        Route::post('/', [DocumentCirculationController::class, 'store'])->name('store');
        Route::get('/{document}/file', [DocumentCirculationController::class, 'file'])->name('file');
        Route::get('/{document}', [DocumentCirculationController::class, 'show'])->name('show');
        Route::post('/{document}/status', [DocumentCirculationController::class, 'updateStatus'])->name('status');
    });

    Route::get('/po-monitoring/document-circulation', fn () => redirect()->route('presales.index'));
    Route::get('/po-monitoring/document-circulation/create', fn () => redirect()->route('presales.create'));
    Route::get('/po-monitoring/document-circulation/{document}', fn ($document) => redirect()->route('presales.show', $document));
    Route::post('/po-monitoring/document-circulation', [DocumentCirculationController::class, 'store']);
    Route::post('/po-monitoring/document-circulation/{document}/status', [DocumentCirculationController::class, 'updateStatus']);
});
