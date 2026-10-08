<?php

use App\Http\Controllers\Admin\UserApprovalController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DocumentCirculationController;
use App\Http\Controllers\BapssController;
use App\Http\Controllers\CombatSiteController;
use App\Http\Controllers\DataSiteUnlockController;
use App\Http\Controllers\EquipmentRelocationController;
use App\Http\Controllers\InfrastructureDashboardController;
use App\Http\Controllers\InfrastructureUploadController;
use App\Http\Controllers\JaknetContractController;
use App\Http\Controllers\PoHqController;
use App\Http\Controllers\PoVarcostController;
use App\Http\Controllers\RecurringIpasController;
use App\Http\Controllers\RecurringTagihanIpasController;
use App\Http\Controllers\SewaLahanRenewalController;
use App\Http\Controllers\UploadFileController;
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

    Route::prefix('infrastruktur')->name('infrastruktur.')->group(function () {
        Route::get('/', [InfrastructureDashboardController::class, 'index'])->name('index');
        Route::get('/dashboard-data', [InfrastructureDashboardController::class, 'data'])->name('dashboard.data');
        Route::get('/dashboard-details', [InfrastructureDashboardController::class, 'details'])->name('dashboard.details');

        // Upload dataset per modul (halaman khusus, tanpa menu baru).
        Route::get('{dataset}/upload', [InfrastructureUploadController::class, 'create'])
            ->whereIn('dataset', ['sewa-lahan', 'combat', 'recurring-ipas', 'recurring-tagihan-ipas', 'jaknet', 'site-unlock', 'bapss'])
            ->name('upload');
        Route::get('{dataset}/template', [InfrastructureUploadController::class, 'template'])
            ->whereIn('dataset', ['sewa-lahan', 'combat', 'recurring-ipas', 'recurring-tagihan-ipas', 'jaknet', 'site-unlock', 'bapss'])
            ->name('upload.template');
        Route::get('{dataset}/export-excel', [InfrastructureUploadController::class, 'export'])
            ->whereIn('dataset', ['recurring-tagihan-ipas', 'site-unlock'])
            ->name('upload.export');
        Route::post('{dataset}/upload', [InfrastructureUploadController::class, 'store'])
            ->whereIn('dataset', ['sewa-lahan', 'combat', 'recurring-ipas', 'recurring-tagihan-ipas', 'jaknet', 'site-unlock', 'bapss'])
            ->name('upload.store')->middleware('role:admin');

        // -- Combat --
        Route::get('combat/data', [CombatSiteController::class, 'data'])->name('combat.data');
        Route::get('combat/export-excel', [CombatSiteController::class, 'exportExcel'])->name('combat.export-excel');
        Route::resource('combat', CombatSiteController::class)
            ->only(['index', 'edit', 'update', 'destroy'])
            ->parameters(['combat' => 'combat'])
            ->middlewareFor(['edit', 'update', 'destroy'], 'role:admin');

        // -- Sewa Lahan (Renewal) --
        Route::get('sewa-lahan/data', [SewaLahanRenewalController::class, 'data'])->name('sewa-lahan.data');
        Route::get('sewa-lahan/export-excel', [SewaLahanRenewalController::class, 'exportExcel'])->name('sewa-lahan.export-excel');
        Route::get('sewa-lahan/{sewaLahan}/cetak-sip', [SewaLahanRenewalController::class, 'cetakSip'])->name('sewa-lahan.cetak-sip');
        Route::resource('sewa-lahan', SewaLahanRenewalController::class)
            ->only(['index', 'edit', 'update', 'destroy'])
            ->parameters(['sewa-lahan' => 'sewaLahan'])
            ->middlewareFor(['edit', 'update', 'destroy'], 'role:admin');

        // -- Recurring (ANT & Ipas) — read-only --
        Route::get('recurring-ipas', [RecurringIpasController::class, 'index'])->name('recurring-ipas.index');
        Route::get('recurring-ipas/data', [RecurringIpasController::class, 'data'])->name('recurring-ipas.data');
        Route::get('recurring-ipas/export-excel', [RecurringIpasController::class, 'exportExcel'])->name('recurring-ipas.export-excel');
        Route::get('recurring-ipas/export-csv', [RecurringIpasController::class, 'exportCsv'])->name('recurring-ipas.export-csv');

        // -- Recurring (Tagihan Ipas) — read-only --
        Route::get('recurring-tagihan-ipas', [RecurringTagihanIpasController::class, 'index'])->name('recurring-tagihan-ipas.index');
        Route::get('recurring-tagihan-ipas/data', [RecurringTagihanIpasController::class, 'data'])->name('recurring-tagihan-ipas.data');

        // -- Sewa Lahan (Jaknet & Dapot) — read-only --
        Route::get('jaknet', [JaknetContractController::class, 'index'])->name('jaknet.index');
        Route::get('jaknet/data', [JaknetContractController::class, 'data'])->name('jaknet.data');
        Route::get('jaknet/export-excel', [JaknetContractController::class, 'exportExcel'])->name('jaknet.export-excel');

        // -- Data Site Unlock — read-only --
        Route::get('site-unlock', [DataSiteUnlockController::class, 'index'])->name('site-unlock.index');
        Route::get('site-unlock/data', [DataSiteUnlockController::class, 'data'])->name('site-unlock.data');

        // -- BAPSS --
        Route::get('bapss/data', [BapssController::class, 'data'])->name('bapss.data');
        Route::get('bapss/export-excel', [BapssController::class, 'exportExcel'])->name('bapss.export-excel');
        Route::get('bapss/export-csv', [BapssController::class, 'exportCsv'])->name('bapss.export-csv');
        Route::get('bapss/{bapss}/file/{type}', [BapssController::class, 'file'])
            ->whereIn('type', ['bapss', 'dismantle'])
            ->name('bapss.file');
        Route::get('bapss/{bapss}', [BapssController::class, 'show'])->name('bapss.show');
        Route::resource('bapss', BapssController::class)
            ->only(['index', 'edit', 'update', 'destroy'])
            ->parameters(['bapss' => 'bapss'])
            ->middlewareFor(['edit', 'update', 'destroy'], 'role:admin');

        // -- Upload File PDF --
        Route::get('upload-file/data', [UploadFileController::class, 'data'])->name('upload-file.data');
        Route::get('upload-file/{uploadFile}/file', [UploadFileController::class, 'file'])->name('upload-file.file');
        Route::resource('upload-file', UploadFileController::class)
            ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
            ->parameters(['upload-file' => 'uploadFile'])
            ->middlewareFor(['create', 'store', 'edit', 'update', 'destroy'], 'role:admin');
    });

    Route::redirect('/infrastructure-management', '/infrastruktur')->name('concept.infrastructure-management');
});
