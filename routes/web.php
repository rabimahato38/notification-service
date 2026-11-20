<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MessageLogController;
use App\Http\Controllers\ServiceConfigurationController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes (Home & Service Management)
Route::middleware('auth')->group(function () {
    Route::get('/', DashboardController::class)->name('home');

    Route::prefix('logs')->name('logs.')->group(function () {
        Route::get('/', [MessageLogController::class, 'index'])->name('index');
        Route::get('/export', [MessageLogController::class, 'export'])->name('export');
        Route::get('/{log}', [MessageLogController::class, 'show'])->name('show')->whereNumber('log');
    });

    Route::prefix('services')->name('services.')->group(function () {
        Route::get('/', [ServiceConfigurationController::class, 'index'])->name('index');
        Route::post('/', [ServiceConfigurationController::class, 'store'])->name('store');
        Route::put('/{service}', [ServiceConfigurationController::class, 'update'])->name('update');
        Route::post('/{service}/regenerate-key', [ServiceConfigurationController::class, 'regenerateKey'])->name('regenerate-key');
        Route::patch('/{service}/toggle-status', [ServiceConfigurationController::class, 'toggleStatus'])->name('toggle-status');
        Route::patch('/{service}/templates', [ServiceConfigurationController::class, 'updateTemplates'])->name('update-templates');
        Route::delete('/{service}', [ServiceConfigurationController::class, 'destroy'])->name('destroy');
    });
});
