<?php

use App\Http\Controllers\BillingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\ReceiptController;
use Illuminate\Support\Facades\Route;

// ─── Homepage — Diretório de Psicólogos ──────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');

// ─── Dashboard (autenticado) ──────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Pacientes
    Route::resource('patients', PatientController::class);

    // Cobranças
    Route::resource('billing', BillingController::class)->except(['edit', 'update']);
    Route::post('billing/{billing}/pay',       [BillingController::class, 'markPaid'])->name('billing.pay');
    Route::get('billing/{billing}/whatsapp',   [BillingController::class, 'whatsapp'])->name('billing.whatsapp');

    // Recibos
    Route::resource('receipts', ReceiptController::class)->except(['edit', 'update']);
    Route::get('receipts/{receipt}/download',  [ReceiptController::class, 'download'])->name('receipts.download');

    // Fórum
    Route::resource('forum', ForumController::class)->except(['edit', 'update']);
    Route::post('forum/{forum}/reply',         [ForumController::class, 'storeReply'])->name('forum.reply');

});

// ─── Portfólio Público /{username} (sem auth) ─────────────────────────────────
// IMPORTANTE: esta rota deve vir APÓS todas as outras para não conflitar
Route::get('/{username}', [PublicProfileController::class, 'show'])
    ->name('profile.public')
    ->where('username', '^(?!login|register|logout|dashboard|patients|billing|receipts|forum|password|profile|storage|up|verify-email|confirm-password|forgot-password|reset-password)[a-zA-Z0-9_-]+$');

require __DIR__ . '/auth.php';
