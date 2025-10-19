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






    // 📝 Job management (Client)
    Route::post('/dashboard/client/post-job', [DashboardController::class, 'postJob'])->name('client.postJob');
    Route::get('/dashboard/client/applicants/{job}', [DashboardController::class, 'viewApplicants'])->name('client.applicants');
    Route::delete('/dashboard/client/delete-job/{job}', [DashboardController::class, 'deleteJob'])->name('client.deleteJob');
    Route::post('/client/rate/{application}', [DashboardController::class, 'rateWorker'])->name('client.rateWorker');

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


// JOB ACTIONS (shared)
    Route::post('/jobs/apply/{id}', [JobController::class, 'apply'])->name('jobs.apply');
    Route::post('/jobs/complete/{id}', [JobController::class, 'complete'])->name('jobs.complete');

    Route::middleware(['auth'])->group(function () {
    // CLIENT ROUTES
    Route::get('/dashboard/client', [ClientController::class, 'index'])->name('client.dashboard');
    Route::get('/client/post-job', [ClientController::class, 'createJob'])->name('client.postJob');
    Route::post('/client/post-job', [ClientController::class, 'storeJob'])->name('client.storeJob');
    Route::get('/client/my-jobs', [ClientController::class, 'myJobs'])->name('client.myJobs');
    Route::get('/client/messages', [ClientController::class, 'messages'])->name('client.messages');
    Route::get('/client/reviews', [ClientController::class, 'reviews'])->name('client.reviews');

    // WORKER ROUTES
    Route::get('/dashboard/worker', [WorkerController::class, 'index'])->name('worker.dashboard');
    Route::get('/worker/available-jobs', [WorkerController::class, 'availableJobs'])->name('worker.availableJobs');
    Route::get('/worker/applied-jobs', [WorkerController::class, 'appliedJobs'])->name('worker.appliedJobs');
    Route::get('/worker/profile', [WorkerController::class, 'profile'])->name('worker.profile');
    Route::get('/worker/ratings', [WorkerController::class, 'ratings'])->name('worker.ratings');
Route::get('/find-jobs', [WorkerController::class, 'findJobs'])->name('worker.findJobs');
Route::post('/apply/{job}', [ApplicationController::class, 'apply'])->name('apply.job');


    // ADMIN ROUTES
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/workers', [AdminController::class, 'manageWorkers'])->name('admin.workers');
    Route::get('/admin/clients', [AdminController::class, 'manageClients'])->name('admin.clients');
});
Route::get('/dashboard', function () {
    $user = auth()->user();

    if (!$user) {
        return redirect()->route('login');
    }

    return match ($user->role) {
        'worker' => redirect()->route('worker.dashboard'),
        'client' => redirect()->route('client.dashboard'),
        'admin'  => redirect()->route('admin.dashboard'),
        default  => redirect()->route('landing'),
    };
})->name('dashboard');

// Notifications routes
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications/fetch', [NotificationController::class, 'fetch'])->name('notifications.fetch');
    Route::post('/notifications/mark-read', [NotificationController::class, 'markAllRead'])->name('notifications.markRead');

    // New routes for job acceptance and rejection notifications
    Route::post('/notifications/job-accepted/{application}', [NotificationController::class, 'jobAccepted'])
        ->name('notifications.jobAccepted');

    Route::post('/notifications/job-rejected/{application}', [NotificationController::class, 'jobRejected'])
        ->name('notifications.jobRejected');
});
// 💬 CHAT SYSTEM
Route::middleware('auth')->group(function () {
    Route::get('/chat/{jobId}/{receiverId}', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');
    Route::get('/chat/fetch/{jobId}/{receiverId}', [ChatController::class, 'fetch'])->name('chat.fetch');
});

Route::get('/messages', [ChatController::class, 'index'])->name('messages.index');
Route::get('/messages/list', [ChatController::class, 'chatList'])->name('messages.list');
Route::post('/chat/read/{jobId}/{receiverId}', [ChatController::class, 'markAsRead']);
Route::get('/worker/applications', [WorkerController::class, 'applications'])->name('worker.applications');



Route::middleware(['auth'])->group(function () {
    // Other worker routes...

    Route::get('/worker/reviews', [WorkerController::class, 'reviews'])->name('worker.reviews');
});
Route::middleware(['auth'])->group(function () {
    // Other worker routes...
    Route::get('/worker/settings', [WorkerController::class, 'settings'])->name('worker.settings');
});
Route::middleware(['auth'])->group(function () {
    Route::get('/chat/fetch/{job_id}/{receiver_id}', [ChatController::class, 'fetch'])->name('chat.fetch');
Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');
});
