<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\AdminDashboardController;
use Illuminate\Support\Facades\Route;

// Public guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/', function () {
        return redirect()->route('login');
    });
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Secure file viewing & reports (accessible by owner or admin)
    Route::get('/documents/{id}/view', [UserDashboardController::class, 'viewFile'])->name('documents.view');
    Route::get('/documents/{id}/report/{format}', [UserDashboardController::class, 'exportReport'])->name('documents.report');

    // User Dashboard Routes
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::post('/dashboard/upload', [UserDashboardController::class, 'upload'])->name('user.upload');

    // Admin-only Routes
    Route::middleware(\App\Http\Middleware\AdminMiddleware::class)->group(function () {
        Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/admin/documents/{id}/details', [AdminDashboardController::class, 'getDocumentDetails'])->name('admin.document.details');
        
        // User management
        Route::get('/admin/users', [AdminDashboardController::class, 'usersList'])->name('admin.users');
        Route::post('/admin/users', [AdminDashboardController::class, 'storeUser'])->name('admin.users.store');
        Route::put('/admin/users/{id}', [AdminDashboardController::class, 'updateUser'])->name('admin.users.update');
        Route::delete('/admin/users/{id}', [AdminDashboardController::class, 'deleteUser'])->name('admin.users.destroy');
    });
});
