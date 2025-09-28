<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WorkerController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController; // ✅ correct one
use App\Http\Controllers\JobController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\RoleController;

// ---------- Google OAuth ----------
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
// ---------- Role Selection ----------
Route::get('/choose-role', [RoleController::class, 'chooseRole'])->name('choose.role');
Route::post('/choose-role', [RoleController::class, 'storeRole'])->name('store.role');

// ---------- Landing Page ----------
Route::get('/', [LandingController::class, 'index'])->name('landing');

// ---------- Jobs ----------
Route::get('/jobs/{id}', [JobController::class, 'show'])->name('jobs.show');
Route::post('/jobs/{id}/apply', [JobController::class, 'apply'])->name('jobs.apply');

// ---------- Auth ----------
Route::get('/register', [RegisterController::class, 'create'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ---------- Static Pages ----------
Route::view('/about', 'about')->name('about');
Route::view('/contact', 'contact')->name('contact');

// ---------- Worker Dashboard ----------
Route::middleware(['auth', 'role:worker'])->group(function () {
    Route::get('/worker/dashboard', [WorkerController::class, 'dashboard'])->name('worker.dashboard');
    Route::get('/worker/jobs', [WorkerController::class, 'recommendedJobs'])->name('worker.jobs');
    Route::post('/worker/upload-resume', [WorkerController::class, 'uploadResume'])->name('worker.uploadResume');
    Route::get('/worker/profile/setup', [WorkerController::class, 'setupProfile'])->name('worker.profile.setup');
});

// ---------- Client Dashboard ----------
Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('/client/dashboard', [ClientController::class, 'dashboard'])->name('client.dashboard');
    Route::get('/client/jobs', [ClientController::class, 'postedJobs'])->name('client.jobs');
    Route::post('/client/post-job', [ClientController::class, 'postJob'])->name('client.postJob');
});

// ---------- Admin Dashboard ----------
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/workers', [AdminController::class, 'manageWorkers'])->name('admin.workers');
    Route::get('/admin/clients', [AdminController::class, 'manageClients'])->name('admin.clients');
});
