<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    LandingController,
    Auth\LoginController,
    Auth\RegisterController,
    GoogleController,
    FirebaseAuthController,
    DashboardController,
    JobController,
    ReviewController,
    AdminController,
    ProfileController,
    UserController
};
use App\Http\Middleware\RoleMiddleware;

/*
|--------------------------------------------------------------------------
| 🧩 Middleware Alias
|--------------------------------------------------------------------------
*/
app('router')->aliasMiddleware('role', RoleMiddleware::class);

/*
|--------------------------------------------------------------------------
| 🌍 Public Routes
|--------------------------------------------------------------------------
*/

// Landing Page
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
Route::middleware('auth')->group(function () {
    Route::get('/apply/{id}', [JobController::class, 'apply'])->name('jobs.apply');
    Route::get('/switch-mode', [UserController::class, 'switchMode'])->name('switch.mode');
});

/*
|--------------------------------------------------------------------------
| 🧍 Onboarding (Profile Setup)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/onboarding', [ProfileController::class, 'showOnboarding'])->name('profile.onboarding');
    Route::post('/onboarding', [ProfileController::class, 'store'])->name('profile.store');
});

/*
|--------------------------------------------------------------------------
| 🧭 Authenticated Dashboards
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // 👷 Worker Dashboard
    Route::get('/dashboard/worker', [DashboardController::class, 'workerDashboard'])
        ->name('worker.dashboard');

    // 🧑‍💼 Client Dashboard
    Route::get('/dashboard/client', [DashboardController::class, 'clientDashboard'])
        ->name('client.dashboard');

    // 📝 Job management (Client)
    Route::post('/dashboard/client/post-job', [DashboardController::class, 'postJob'])->name('client.postJob');
    Route::get('/dashboard/client/applicants/{job}', [DashboardController::class, 'viewApplicants'])->name('client.applicants');
    Route::delete('/dashboard/client/delete-job/{job}', [DashboardController::class, 'deleteJob'])->name('client.deleteJob');
    Route::post('/client/rate/{application}', [DashboardController::class, 'rateWorker'])->name('client.rateWorker');

    // 🔄 Auto Redirect After Login
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
Route::middleware(['auth'])->group(function () {
    Route::post('/applications/{application}/accept', [DashboardController::class, 'acceptApplication'])->name('applications.accept');
    Route::post('/applications/{application}/reject', [DashboardController::class, 'rejectApplication'])->name('applications.reject');
});

/*
|--------------------------------------------------------------------------
| 💬 Reviews
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews/store', [ReviewController::class, 'store'])->name('reviews.store');
});

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
