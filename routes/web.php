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
    UserController,
    ClientController,
WorkerController,
ApplicationController,
NotificationController,
ChatController,
ReportController,


};
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;

Route::post('/contact', function (Request $request) {
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'message' => 'required|string',
    ]);

    // Send the email
    Mail::to('yourgmail@gmail.com')->send(new ContactMail($data));

    return back()->with('success', '✅ Thank you ' . $data['name'] . '! Your message has been sent successfully.');
});
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

Route::put('/applications/{id}/status', [ApplicationController::class, 'updateStatus'])->name('applications.updateStatus');
Route::get('/client/applications', [ClientController::class, 'viewApplications'])
    ->name('client.applications')
    ->middleware('auth');


// Worker Profile
Route::middleware('auth')->group(function () {
    Route::get('/worker/profile', [ProfileController::class, 'showForm'])->name('worker.profile');
    Route::post('/worker/profile', [ProfileController::class, 'saveProfile'])->name('worker.profile.save');
});








    //🔄 Auto Redirect After Login
    Route::get('/dashboard', function () {
        $user = auth()->user();
        return match ($user->role) {
            'worker' => redirect()->route('worker.dashboard'),
            'client' => redirect()->route('client.dashboard'),
            'admin'  => redirect()->route('admin.dashboard'),
            default  => redirect()->route('landing'),
        };
    })->name('dashboard');



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
| 🧱 Job Actions
|--------------------------------------------------------------------------
*/
Route::post('/jobs/apply/{id}', [JobController::class, 'apply'])->name('jobs.apply');
Route::post('/jobs/complete/{id}', [JobController::class, 'complete'])->name('jobs.complete');

/*
|--------------------------------------------------------------------------
| 👥 Client Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard/client/post-job', [ClientController::class, 'createJob'])->name('client.postJob');
    Route::post('/dashboard/client/post-job', [ClientController::class, 'storeJob'])->name('client.storeJob');
    Route::get('/dashboard/client/my-jobs', [ClientController::class, 'myJobs'])->name('client.my-jobs');
    Route::get('/dashboard/client/applications', [ClientController::class, 'viewApplications'])->name('client.applications');
    Route::get('/dashboard/client/messages', [ClientController::class, 'messages'])->name('messages.index');
    Route::get('/dashboard/client/reviews', [ClientController::class, 'reviews'])->name('client.reviews');
    Route::get('/dashboard/client', [ClientController::class, 'index'])->name('client.dashboard');

});


/*
|--------------------------------------------------------------------------
| 🧰 Worker Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/worker', [WorkerController::class, 'index'])->name('worker.dashboard');
    Route::get('/worker/available-jobs', [WorkerController::class, 'availableJobs'])->name('worker.availableJobs');
    Route::get('/worker/applied-jobs', [WorkerController::class, 'appliedJobs'])->name('worker.appliedJobs');
    Route::get('/worker/profile', [WorkerController::class, 'profile'])->name('worker.profile');
    Route::get('/worker/ratings', [WorkerController::class, 'ratings'])->name('worker.ratings');
    Route::get('/worker/applications', [WorkerController::class, 'applications'])->name('worker.applications');
    Route::get('/worker/reviews', [WorkerController::class, 'reviews'])->name('worker.reviews');
    Route::get('/worker/settings', [WorkerController::class, 'settings'])->name('worker.settings');
    Route::get('/find-jobs', [WorkerController::class, 'findJobs'])->name('worker.findJobs');
    Route::post('/apply/{job}', [ApplicationController::class, 'apply'])->name('apply.job');
});

/*
|--------------------------------------------------------------------------
| 🧑‍💼 Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/workers', [AdminController::class, 'manageWorkers'])->name('workers');
    Route::get('/clients', [AdminController::class, 'manageClients'])->name('clients');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPDF'])->name('reports.pdf');
    Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.excel');

    // User management
    Route::get('/users', [AdminController::class, 'manageUsers'])->name('users');
    Route::post('/users/activate/{id}', [AdminController::class, 'activateUser'])->name('users.activate');
    Route::post('/users/deactivate/{id}', [AdminController::class, 'deactivateUser'])->name('users.deactivate');
    Route::delete('/users/delete/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');

    // Job management
    Route::get('/jobs', [AdminController::class, 'manageJobs'])->name('jobs');
    Route::post('/jobs/approve/{id}', [AdminController::class, 'approveJob'])->name('jobs.approve');
    Route::post('/jobs/complete/{id}', [AdminController::class, 'completeJob'])->name('jobs.complete');
    Route::delete('/jobs/delete/{id}', [AdminController::class, 'deleteJob'])->name('jobs.delete');
});

/*
|--------------------------------------------------------------------------
| 🔔 Notifications
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications/fetch', [NotificationController::class, 'fetch'])->name('notifications.fetch');
    Route::post('/notifications/mark-read', [NotificationController::class, 'markAllRead'])->name('notifications.markRead');
    Route::post('/notifications/job-accepted/{application}', [NotificationController::class, 'jobAccepted'])->name('notifications.jobAccepted');
    Route::post('/notifications/job-rejected/{application}', [NotificationController::class, 'jobRejected'])->name('notifications.jobRejected');
});

/*
|--------------------------------------------------------------------------
| 💬 Chat System
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/chat/{jobId}/{receiverId}', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/fetch/{jobId}/{receiverId}', [ChatController::class, 'fetch'])->name('chat.fetch');
    Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');
    Route::post('/chat/read/{jobId}/{receiverId}', [ChatController::class, 'markAsRead']);
    Route::get('/messages/list', [ChatController::class, 'chatList'])->name('messages.list');
});

/*
|--------------------------------------------------------------------------
| 🧭 Universal Dashboard Redirect
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| 🔍 Search (AJAX)
|--------------------------------------------------------------------------
*/
Route::get('/search/jobs', [WorkerController::class, 'searchJobs'])->name('search.jobs');

Route::get('/search/jobs', [App\Http\Controllers\WorkerController::class, 'searchJobs'])->name('search.jobs');
Route::post('/apply/{job}', [App\Http\Controllers\ApplicationController::class, 'apply'])->name('apply.job');
Route::get('/admin/settings', function () {
    return view('admin.settings');
})->name('admin.settings');

Route::get('/admin/reports/export/pdf', [App\Http\Controllers\ReportController::class, 'exportPDF'])->name('reports.pdf');
