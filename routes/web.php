<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

// Home - Redirect to search
Route::get('/', function () {
    return redirect()->route('search.index');
});

// Admin Routes - GPX Management
Route::resource('routes', RouteController::class)->except(['edit', 'update']);
Route::get('/routes-batch', [RouteController::class, 'createBatch'])->name('routes.batch');
Route::post('/routes-batch', [RouteController::class, 'storeBatch'])->name('routes.batch.store');

// User Routes - Search
Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::post('/search', [SearchController::class, 'search'])->name('search.submit');

// Admin Dashboard
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/export-csv', [AdminController::class, 'exportCsv'])->name('export.csv');
    Route::get('/export-embeddings', [AdminController::class, 'exportEmbeddings'])->name('export.embeddings');
});
