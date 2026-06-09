<?php

use App\Http\Controllers\ExportController;
use App\Livewire\AnomaliesList;
use App\Livewire\Dashboard;
use App\Livewire\ExportsList;
use App\Livewire\ImportJournal;
use App\Livewire\TransactionsList;
use Illuminate\Support\Facades\Route;

// ── Dashboard ──────────────────────────────────────────────────────────────
Route::get('/', Dashboard::class)->name('dashboard');
Route::get('/dashboard', Dashboard::class)->name('dashboard');

// ── Journaux ───────────────────────────────────────────────────────────────
Route::get('/journaux/importer', ImportJournal::class)->name('journals.import');
Route::get('/rapports/exports', ExportsList::class)->name('exports.index');

// ── Transactions ───────────────────────────────────────────────────────────
Route::get('/transactions', TransactionsList::class)->name('transactions.index');

// ── Anomalies ──────────────────────────────────────────────────────────────
Route::get('/anomalies', AnomaliesList::class)->name('anomalies.index');

// ── Exports ────────────────────────────────────────────────────────────────
Route::prefix('exports')->name('export.')->group(function () {
    Route::get('/transactions/{journal}/{type?}', [ExportController::class, 'invoicesExcel'])
        ->name('invoices.excel');
    Route::get('/articles/{journal}', [ExportController::class, 'articlesExcel'])
        ->name('articles.excel');
    Route::get('/complet/{journal}', [ExportController::class, 'fullReportExcel'])
        ->name('full.excel');
    Route::get('/conformite/{journal}/pdf', [ExportController::class, 'compliancePdf'])
        ->name('compliance.pdf');
    Route::get('/tva/{journal}/pdf', [ExportController::class, 'tvaSummaryPdf'])
        ->name('tva.pdf');
    Route::get('/tva/{journal}/pdf-journalier', [ExportController::class, 'tvaDailyPdf'])
        ->name('tva.daily.pdf');
    Route::get('/tva/{journal}/excel', [ExportController::class, 'tvaDetailedExcel'])
        ->name('tva.excel');
});
