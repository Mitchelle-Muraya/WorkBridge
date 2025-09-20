<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WorkerController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\GoogleController;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\LandingController;

// ---------- Landing Page ----------
Route::get('/', [LandingController::class, 'index'])->name('landing');

// ---------- Jobs ----------
Route::get('/jobs/{id}', [JobController::class, 'show'])->name('jobs.show');
Route::post('/jobs/{id}/apply', [JobController::class, 'apply'])->name('jobs.apply');

// ---------- Registration ----------
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

// ---------- Login & Logout ----------
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ---------- Google OAuth ----------
Route::get('auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

// ---------- Static Pages ----------
Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

// ---------- Worker Dashboard ----------
Route::middleware('auth:worker')->group(function () {
    Route::get('/worker/dashboard', [WorkerController::class, 'dashboard'])->name('worker.dashboard');
    Route::get('/worker/jobs', [WorkerController::class, 'recommendedJobs'])->name('worker.jobs');
    Route::post('/worker/upload-resume', [WorkerController::class, 'uploadResume'])->name('worker.uploadResume');
});

// ---------- Client Dashboard ----------
Route::middleware('auth:client')->group(function () {
    Route::get('/client/dashboard', [ClientController::class, 'dashboard'])->name('client.dashboard');
    Route::get('/client/jobs', [ClientController::class, 'postedJobs'])->name('client.jobs');
    Route::post('/client/post-job', [ClientController::class, 'postJob'])->name('client.postJob');
});

// ---------- Admin Dashboard ----------
Route::middleware('auth:admin')->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/workers', [AdminController::class, 'manageWorkers'])->name('admin.workers');
    Route::get('/admin/clients', [AdminController::class, 'manageClients'])->name('admin.clients');
});
// Worker profile setup (after registration)
Route::get('/worker/profile/setup', [WorkerController::class, 'setupProfile'])
    ->name('worker.profile.setup');
