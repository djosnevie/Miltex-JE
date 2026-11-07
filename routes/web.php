<?php

use App\Http\Controllers\ExportController;
use App\Livewire\AnomaliesList;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\TwoFactorChallenge;
use App\Livewire\Dashboard;
use App\Livewire\ExportsList;
use App\Livewire\ImportJournal;
use App\Livewire\PointsOfSaleManagement;
use App\Livewire\SuperAdminHub;
use App\Livewire\TransactionsList;
use App\Livewire\UserManagement;
use App\Livewire\UserProfile;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PasskeyController;
use Illuminate\Support\Facades\Route;

// ── Auth ───────────────────────────────────────────────────────────────────
Route::get('/login', Login::class)->name('login')->middleware('guest');
Route::get('/two-factor-challenge', TwoFactorChallenge::class)->name('2fa.challenge')->middleware('guest');

// ── Passkeys / WebAuthn Biometrics ─────────────────────────────────────────
Route::post('/passkeys/login-options', [PasskeyController::class, 'loginOptions'])->name('passkeys.login.options');
Route::post('/passkeys/login',         [PasskeyController::class, 'login'])->name('passkeys.login');

Route::middleware('auth')->group(function () {
    Route::get('/passkeys/register-options', [PasskeyController::class, 'registerOptions'])->name('passkeys.register.options');
    Route::post('/passkeys/register',        [PasskeyController::class, 'register'])->name('passkeys.register');
    Route::delete('/passkeys/{passkey}',     [PasskeyController::class, 'destroy'])->name('passkeys.destroy');
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout')->middleware('auth');

// ── Protected Routes ────────────────────────────────────────────────────────
Route::middleware(['auth', 'role'])->group(function () {

    // ── Dashboard ──────────────────────────────────────────────────────────
    Route::get('/',          Dashboard::class)->name('dashboard');
    Route::get('/dashboard', Dashboard::class);

    // ── Profil Utilisateur (tous rôles) ────────────────────────────────────
    Route::get('/profil', UserProfile::class)->name('profile.show');

    // ── SuperAdmin Hub (super_admin seulement) ─────────────────────────────
    Route::get('/super-admin', SuperAdminHub::class)
        ->name('superadmin.index')
        ->middleware('role:super_admin');

    // ── Transactions (lecture — tous rôles) ────────────────────────────────
    Route::get('/transactions', TransactionsList::class)->name('transactions.index');

    // ── Anomalies (lecture — tous rôles) ──────────────────────────────────
    Route::get('/anomalies', AnomaliesList::class)->name('anomalies.index');

    // ── Import (admin seulement) ───────────────────────────────────────────
    Route::get('/journaux/importer', ImportJournal::class)
        ->name('journals.import')
        ->middleware('role:admin,super_admin');

    // ── Exports ────────────────────────────────────────────────────────────
    Route::get('/rapports/exports', ExportsList::class)
        ->name('exports.index')
        ->middleware('role:admin,super_admin');

    // ── Points de Vente (admin seulement) ──────────────────────────────────
    Route::get('/points-de-vente', PointsOfSaleManagement::class)
        ->name('points-of-sale.index')
        ->middleware('role:admin,super_admin');

    // ── Gestion des utilisateurs (admin seulement) ─────────────────────────
    Route::get('/utilisateurs', UserManagement::class)
        ->name('users.index')
        ->middleware('role:admin,super_admin');

    // ── API Export downloads (admin seulement) ─────────────────────────────
    Route::prefix('exports')->name('export.')->middleware('role:admin,super_admin')->group(function () {
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

        // ── POS Exports ──────────────────────────────────────────────────
        Route::get('/pdv/transactions/{posId}/{type?}', [ExportController::class, 'posInvoicesExcel'])
            ->name('pos.invoices.excel');
        Route::get('/pdv/articles/{posId}', [ExportController::class, 'posArticlesExcel'])
            ->name('pos.articles.excel');
        Route::get('/pdv/complet/{posId}', [ExportController::class, 'posFullReportExcel'])
            ->name('pos.full.excel');
        Route::get('/pdv/tva/{posId}/excel', [ExportController::class, 'posTvaDetailedExcel'])
            ->name('pos.tva.excel');
        Route::get('/pdv/conformite/{posId}/pdf', [ExportController::class, 'posCompliancePdf'])
            ->name('pos.compliance.pdf');
        Route::get('/pdv/tva/{posId}/pdf', [ExportController::class, 'posTvaSummaryPdf'])
            ->name('pos.tva.pdf');
        Route::get('/pdv/tva/{posId}/pdf-journalier', [ExportController::class, 'posTvaDailyPdf'])
            ->name('pos.tva.daily.pdf');

        // ── Global Exports (Tous les PDV / Consolidé National) ─────────────
        Route::get('/global/transactions/{type?}', [ExportController::class, 'globalInvoicesExcel'])
            ->name('global.invoices.excel');
        Route::get('/global/articles', [ExportController::class, 'globalArticlesExcel'])
            ->name('global.articles.excel');
        Route::get('/global/complet', [ExportController::class, 'globalFullReportExcel'])
            ->name('global.full.excel');
        Route::get('/global/tva/excel', [ExportController::class, 'globalTvaDetailedExcel'])
            ->name('global.tva.excel');
        Route::get('/global/conformite/pdf', [ExportController::class, 'globalCompliancePdf'])
            ->name('global.compliance.pdf');
        Route::get('/global/tva/pdf', [ExportController::class, 'globalTvaSummaryPdf'])
            ->name('global.tva.pdf');
        Route::get('/global/tva/pdf-journalier', [ExportController::class, 'globalTvaDailyPdf'])
            ->name('global.tva.daily.pdf');
    });
});
