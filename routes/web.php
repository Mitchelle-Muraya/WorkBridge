<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\FirebaseAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\RoleMiddleware;
use App\Models\Job;

// ✅ Middleware Alias
app('router')->aliasMiddleware('role', RoleMiddleware::class);

/*
|--------------------------------------------------------------------------
| 🌍 Public Routes
|--------------------------------------------------------------------------
*/

// Landing Page (Homepage)
Route::get('/', [LandingController::class, 'index'])->name('landing');

// Static pages
Route::view('/about', 'about')->name('about');
Route::view('/contact', 'contact')->name('contact');

/*
|--------------------------------------------------------------------------
| 🔐 Authentication & Firebase Routes
|--------------------------------------------------------------------------
*/

// Firebase OTP Auth
Route::post('/firebase/verify', [FirebaseAuthController::class, 'verifyToken'])->name('firebase.verify');

// Google Login
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// Standard Registration & Login
Route::get('/register', [RegisterController::class, 'create'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| 💼 Job Browsing & Application
|--------------------------------------------------------------------------
*/

// Publicly visible jobs (homepage)
Route::get('/', [LandingController::class, 'index'])->name('landing');

// Apply for job (authenticated users only)
Route::middleware('auth')->group(function () {
    Route::get('/apply/{id}', [JobController::class, 'apply'])->name('jobs.apply');
});

// Role switching (Worker ↔ Client)
Route::get('/switch-mode', [UserController::class, 'switchMode'])
    ->middleware('auth')
    ->name('switch.mode');

/*
|--------------------------------------------------------------------------
| 🧭 Authenticated Dashboards
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // 👷 Worker Dashboard
    Route::get('/dashboard/worker', [DashboardController::class, 'workerDashboard'])->name('worker.dashboard');

    // 🧑‍💼 Client Dashboard
    Route::get('/dashboard/client', [DashboardController::class, 'clientDashboard'])->name('client.dashboard');
    Route::post('/dashboard/client/post-job', [DashboardController::class, 'postJob'])->name('client.postJob');
    Route::get('/dashboard/client/applicants/{job}', [DashboardController::class, 'viewApplicants'])->name('client.applicants');
    Route::delete('/dashboard/client/delete-job/{job}', [DashboardController::class, 'deleteJob'])->name('client.deleteJob');
    Route::post('/client/rate/{application}', [DashboardController::class, 'rateWorker'])->name('client.rateWorker');

    // ✅ Onboarding
    Route::get('/onboarding', [ProfileController::class, 'showOnboarding'])->name('profile.onboarding');
    Route::post('/onboarding', [ProfileController::class, 'store'])->name('profile.store');

    // 🔄 Auto redirect after login
    Route::get('/dashboard', function () {
        $user = auth()->user();

        return match ($user->role) {
            'worker' => redirect()->route('worker.dashboard'),
            'client' => redirect()->route('client.dashboard'),
            'admin'  => redirect()->route('admin.dashboard'),
            default  => redirect()->route('landing'),
        };
    })->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| 📨 Applications
|--------------------------------------------------------------------------
*/
Route::post('/applications/{application}/accept', [DashboardController::class, 'acceptApplication'])->name('applications.accept');
Route::post('/applications/{application}/reject', [DashboardController::class, 'rejectApplication'])->name('applications.reject');

/*
|--------------------------------------------------------------------------
| 🧑‍💻 Admin Dashboard
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/workers', [AdminController::class, 'manageWorkers'])->name('admin.workers');
    Route::get('/admin/clients', [AdminController::class, 'manageClients'])->name('admin.clients');
});
Route::middleware(['auth', 'profile.complete'])->group(function () {
    Route::get('/onboarding', [ProfileController::class, 'showOnboarding'])->name('profile.onboarding');
    Route::post('/onboarding', [ProfileController::class, 'store'])->name('profile.store');
});

