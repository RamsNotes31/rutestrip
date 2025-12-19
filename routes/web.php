<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

// Home - Redirect to search
Route::get('/', function () {
    return redirect()->route('search.index');
});

// Public Routes - GPX Upload (available to everyone for now)
Route::resource('routes', RouteController::class)->except(['edit', 'update']);
Route::get('/routes-batch', [RouteController::class, 'createBatch'])->name('routes.batch');
Route::post('/routes-batch', [RouteController::class, 'storeBatch'])->name('routes.batch.store');

// User Routes - Search
Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::post('/search', [SearchController::class, 'search'])->name('search.submit');

// Admin Authentication
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login'); // Required by auth middleware
Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Admin Dashboard (Protected)
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/export-csv', [AdminController::class, 'exportCsv'])->name('export.csv');
    Route::get('/export-embeddings', [AdminController::class, 'exportEmbeddings'])->name('export.embeddings');
});
