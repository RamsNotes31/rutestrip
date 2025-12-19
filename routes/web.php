<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Home - Redirect to search
Route::get('/', function () {
    return redirect()->route('search.index');
});

// Public Routes - GPX Upload & View
Route::get('/routes', [RouteController::class, 'index'])->name('routes.index');
Route::get('/routes/create', [RouteController::class, 'create'])->name('routes.create');
Route::post('/routes', [RouteController::class, 'store'])->name('routes.store');
Route::get('/routes/{route}', [RouteController::class, 'show'])->name('routes.show');
Route::get('/routes/{route}/similar', [RouteController::class, 'similarRoutes'])->name('routes.similar');
Route::get('/routes-batch', [RouteController::class, 'createBatch'])->name('routes.batch');
Route::post('/routes-batch', [RouteController::class, 'storeBatch'])->name('routes.batch.store');

// Reviews & Ratings (Auth required)
Route::middleware('auth')->group(function () {
    Route::post('/routes/{route}/review', [RouteController::class, 'storeReview'])->name('routes.review.store');
    Route::post('/routes/{route}/like', [RouteController::class, 'toggleLike'])->name('routes.like.toggle');
});

// Admin Only - Delete Route
Route::delete('/routes/{route}', [RouteController::class, 'destroy'])
    ->name('routes.destroy')
    ->middleware('auth');

// Search
Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::post('/search', [SearchController::class, 'search'])->name('search.submit');

// Documentation
Route::get('/arsitektur', function () {
    return view('architecture');
})->name('architecture');

// User Authentication (Guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
    Route::get('/admin/login', [AuthController::class, 'showAdminLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login.submit');
});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// User Dashboard
Route::prefix('user')->name('user.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/stats', [UserController::class, 'statsApi'])->name('dashboard.stats');
    Route::get('/favorites', [UserController::class, 'favorites'])->name('favorites');
    Route::post('/favorite/{route}', [UserController::class, 'toggleFavorite'])->name('favorite.toggle');
    Route::get('/history', [UserController::class, 'history'])->name('history');
    Route::get('/profile', [UserController::class, 'editProfile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::put('/password', [UserController::class, 'updatePassword'])->name('password.update');
});

// Admin Dashboard
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/export-csv', [AdminController::class, 'exportCsv'])->name('export.csv');
    Route::get('/export-embeddings', [AdminController::class, 'exportEmbeddings'])->name('export.embeddings');
});
