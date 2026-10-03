<?php

use App\Http\Controllers\Admin\AlumniController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\RegistrationController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DirectoryController;
use App\Http\Controllers\EventRegistrationController;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\WallController;
use App\Http\Controllers\WallNotificationController;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', fn () => view('welcome'))->name('home');

// Serve storage files without needing storage:link symlink (cPanel shared hosting)
Route::get('/storage/{path}', function ($path) {
    $file = storage_path('app/public/' . $path);
    if (!file_exists($file)) abort(404);
    return response()->file($file);
})->where('path', '.*');

// Auth — guest only, rate limited
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])
        ->middleware('throttle:3,1');

    Route::get('/login', [LoginController::class, 'showForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])
        ->middleware('throttle:5,1');

    Route::get('/verify-otp', [OtpController::class, 'showForm'])->name('otp.form');
    Route::post('/verify-otp', [OtpController::class, 'verify'])->name('otp.verify')
        ->middleware('throttle:10,1');
    Route::post('/verify-otp/resend', [OtpController::class, 'resend'])->name('otp.resend')
        ->middleware('throttle:3,1');
});

// Pending approval — informational
Route::get('/pending', fn () => view('auth.pending'))->name('pending');

// SSLCommerz IPN — no auth, no CSRF (server-to-server POST from SSLCommerz)
Route::post('/payment/ipn', [PaymentController::class, 'ipn'])
    ->name('payment.ipn')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// Authenticated + verified alumni
Route::middleware(['auth', 'alumni.verified'])->group(function () {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Event registration
    Route::get('/event/register', [EventRegistrationController::class, 'show'])->name('event.show');
    Route::post('/event/register', [EventRegistrationController::class, 'save'])->name('event.save');

    // Alumni directory
    Route::get('/directory', [DirectoryController::class, 'index'])->name('directory');

    // Wall
    Route::get('/wall', [WallController::class, 'index'])->name('wall.index');
    Route::post('/wall', [WallController::class, 'store'])->name('wall.store');
    Route::get('/wall/poll', [WallController::class, 'poll'])->name('wall.poll');
    Route::get('/wall/post/{post}', [WallController::class, 'goToPost'])->name('wall.go-to-post');
    Route::get('/wall/search-alumni', [WallController::class, 'searchAlumni'])->name('wall.search');
    Route::patch('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::patch('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/posts/{post}/react', [ReactionController::class, 'togglePost'])->name('posts.react');
    Route::post('/comments/{comment}/react', [ReactionController::class, 'toggleComment'])->name('comments.react');
    Route::get('/posts/{post}/reactions', [ReactionController::class, 'listPost'])->name('posts.reactions.list');
    Route::get('/comments/{comment}/reactions', [ReactionController::class, 'listComment'])->name('comments.reactions.list');

    // Wall notifications
    Route::get('/wall/notifications', [WallNotificationController::class, 'index'])->name('wall.notifications');
    Route::post('/wall/notifications/read', [WallNotificationController::class, 'markRead'])->name('wall.notifications.read');

    // Theme & language preference
    Route::post('/settings/theme', [ProfileController::class, 'saveTheme'])->name('settings.theme');
    Route::post('/settings/lang',  [ProfileController::class, 'saveLang'])->name('settings.lang');

    // Payment
    Route::get('/payment/confirm', [PaymentController::class, 'confirm'])->name('payment.confirm');
    Route::post('/payment/initiate', [PaymentController::class, 'initiate'])->name('payment.initiate');
    Route::post('/payment/manual', [PaymentController::class, 'manual'])->name('payment.manual');
    Route::post('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::post('/payment/fail', [PaymentController::class, 'fail'])->name('payment.fail');
    Route::post('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
});

// Admin only
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/alumni', [AlumniController::class, 'index'])->name('alumni.index');
    Route::get('/alumni/{alumnus}', [AlumniController::class, 'show'])->name('alumni.show');
    Route::post('/alumni/{alumnus}/verify', [AlumniController::class, 'verify'])->name('alumni.verify');
    Route::post('/alumni/{alumnus}/reject', [AlumniController::class, 'reject'])->name('alumni.reject');

    Route::post('/alumni/{alumnus}/notes', [AlumniController::class, 'storeNote'])->name('alumni.notes.store');
    Route::delete('/notes/{note}', [AlumniController::class, 'destroyNote'])->name('notes.destroy');

    Route::post('/alumni/{alumnus}/payment/confirm', [AlumniController::class, 'confirmPayment'])->name('alumni.payment.confirm');
    Route::post('/alumni/{alumnus}/payment/reset', [AlumniController::class, 'resetPayment'])->name('alumni.payment.reset');
    Route::post('/alumni/{alumnus}/payment/adjust', [AlumniController::class, 'adjustPayment'])->name('alumni.payment.adjust');

    Route::get('/registrations', [RegistrationController::class, 'index'])->name('registrations.index');

    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');

    Route::get('/export/alumni', [ExportController::class, 'alumni'])->name('export.alumni');
    Route::get('/export/registrations', [ExportController::class, 'registrations'])->name('export.registrations');
});

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');
