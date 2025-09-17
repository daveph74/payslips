<?php

use App\Http\Controllers\PayrollImportController;
use App\Http\Controllers\PayslipController;
use Illuminate\Support\Facades\Route;

// Home route - redirect to payroll upload
Route::get('/', function () {
    return redirect()->route('payroll.upload');
});

// Payroll Import Routes
Route::prefix('payroll')->name('payroll.')->group(function () {
    Route::get('/upload', [PayrollImportController::class, 'showUploadForm'])->name('upload');
    Route::post('/upload', [PayrollImportController::class, 'processUpload'])->name('upload.process');
    Route::get('/', [PayrollImportController::class, 'index'])->name('index');
});

// Payslip Generation Routes
Route::prefix('payslips')->name('payslips.')->group(function () {
    Route::get('/{payroll}', [PayslipController::class, 'generatePayslip'])->name('generate');
    Route::get('/{payroll}/preview', [PayslipController::class, 'previewPayslip'])->name('preview');
    Route::get('/{payroll}/download', [PayslipController::class, 'downloadPayslip'])->name('download');
    Route::post('/batch', [PayslipController::class, 'generateBatch'])->name('batch');
});
