<?php

use App\Http\Controllers\Admin\UserApprovalController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\EquipmentRelocationController;
use App\Http\Controllers\PoHqController;
use App\Http\Controllers\PoVarcostController;
use App\Http\Controllers\PresalesController;
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

    Route::get('/equipment-relocation', [EquipmentRelocationController::class, 'index'])->name('equipment-relocation.index');
    Route::get('/equipment-relocation/inventory-data', [EquipmentRelocationController::class, 'inventoryData'])->name('equipment-relocation.inventory-data');
    Route::get('/equipment-relocation/relocation-data', [EquipmentRelocationController::class, 'relocationData'])->name('equipment-relocation.relocation-data');
    Route::post('/equipment-relocation/relocation-data', [EquipmentRelocationController::class, 'saveRelocation'])->name('equipment-relocation.relocation-data.store');
    Route::delete('/equipment-relocation/relocation-data', [EquipmentRelocationController::class, 'deleteRelocation'])->name('equipment-relocation.relocation-data.destroy');
    Route::get('/equipment-relocation/export-excel', [EquipmentRelocationController::class, 'exportExcel'])->name('equipment-relocation.export-excel');
    Route::post('/equipment-relocation/import-excel', [EquipmentRelocationController::class, 'importExcel'])->name('equipment-relocation.import-excel');
    Route::redirect('/rru', '/equipment-relocation')->name('rru.index');

    Route::prefix('po-monitoring/presales')->name('presales.')->group(function () {
        Route::get('/', [PresalesController::class, 'index'])->name('index');
        Route::get('/create', [PresalesController::class, 'create'])->name('create');
        Route::post('/', [PresalesController::class, 'store'])->name('store');
        Route::get('/{document}/file', [PresalesController::class, 'file'])->name('file');
        Route::get('/{document}', [PresalesController::class, 'show'])->name('show');
        Route::post('/{document}/status', [PresalesController::class, 'updateStatus'])->name('status');
    });

    Route::get('/po-monitoring/document-circulation', fn () => redirect()->route('presales.index'));
    Route::get('/po-monitoring/document-circulation/create', fn () => redirect()->route('presales.create'));
    Route::get('/po-monitoring/document-circulation/{document}', fn ($document) => redirect()->route('presales.show', $document));
    Route::post('/po-monitoring/document-circulation', [PresalesController::class, 'store']);
    Route::post('/po-monitoring/document-circulation/{document}/status', [PresalesController::class, 'updateStatus']);


    Route::get('/po-hq/data', [PoHqController::class, 'data'])->name('po-hq.data');

    // Satu resource utuh (jangan dipecah dua: route show /po-hq/{po_hq}
    // akan membayangi /po-hq/create jika resource mutasi dideklarasikan
    // sesudahnya). Proteksi mutasi diterapkan per-method via middlewareFor().
    Route::resource('po-hq', PoHqController::class)
        ->middlewareFor(['create', 'store', 'edit', 'update', 'destroy'], 'role:admin');

    Route::get('/po-varcost/data', [PoVarcostController::class, 'data'])->name('po-varcost.data');
    Route::get('/po-varcost/export-excel', [PoVarcostController::class, 'exportExcel'])->name('po-varcost.export-excel');
    Route::get('/po-varcost', [PoVarcostController::class, 'index'])->name('po-varcost.index');
    Route::post('/po-varcost', [PoVarcostController::class, 'store'])->name('po-varcost.store')->middleware('role:admin');
    Route::put('/po-varcost/{po_varcost}', [PoVarcostController::class, 'update'])->name('po-varcost.update')->middleware('role:admin');
    Route::delete('/po-varcost/{po_varcost}', [PoVarcostController::class, 'destroy'])->name('po-varcost.destroy')->middleware('role:admin');

});
