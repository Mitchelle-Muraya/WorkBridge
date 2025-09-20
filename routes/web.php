<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WorkerController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\GoogleController;

use Laravel\Socialite\Facades\Socialite;

Route::get('auth/google', function () {
    return Socialite::driver('google')->redirect();
});

Route::get('auth/google/callback', function () {
    $user = Socialite::driver('google')->user();
    dd($user); // For testing
});


Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/', function () {
    return view('landing');
});

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');


Route::get('/login', function () {
    return view('auth.login'); // points to resources/views/auth/login.blade.php
});


Route::get('auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');


// ---------- Authentication ----------
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login/worker', [AuthController::class, 'loginWorker'])->name('login.worker');
Route::post('/login/client', [AuthController::class, 'loginClient'])->name('login.client');
Route::post('/login/admin', [AuthController::class, 'loginAdmin'])->name('login.admin');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

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


