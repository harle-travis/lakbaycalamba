<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\StampController;
use App\Http\Controllers\TouristController;
use App\Http\Controllers\FavoriteController;

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/signup', [AuthController::class, 'showSignup'])->name('signup');
Route::post('/signup', [AuthController::class, 'signup'])->name('signup.submit');

// Password Reset
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Public
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/all-establishments', [App\Http\Controllers\HomeController::class, 'allEstablishments'])->name('all-establishments');
Route::get('/establishment/{establishment}', [App\Http\Controllers\HomeController::class, 'show'])->name('establishment.show');

// Review routes (authenticated)
Route::middleware('auth')->group(function () {
    Route::post('/establishment/{establishmentId}/review', [App\Http\Controllers\ReviewController::class, 'store'])->name('review.store');
    Route::get('/establishment/{establishmentId}/reviews', [App\Http\Controllers\ReviewController::class, 'getEstablishmentReviews'])->name('reviews.get');
    Route::get('/establishment/{establishmentId}/user-review', [App\Http\Controllers\ReviewController::class, 'checkUserReview'])->name('review.check');
    Route::put('/reviews/{reviewId}', [App\Http\Controllers\ReviewController::class, 'update'])->name('review.update');
    Route::delete('/reviews/{reviewId}', [App\Http\Controllers\ReviewController::class, 'destroy'])->name('review.destroy');
});
Route::view('/aboutus', 'aboutus')->name('aboutus');
Route::get('/weather', [WeatherController::class, 'show'])->name('weather');
Route::get('/leaderboard', function() {
    // Get users with their stamp counts, ordered by most stamps
    $leaders = \App\Models\User::withCount('stamps')
        ->where('role', 'tourist') // Only show tourists on leaderboard
        ->orderBy('stamps_count', 'desc')
        ->take(10) // Top 10 users
        ->get();
    
    return view('leaderboard', compact('leaders'));
})->name('leaderboard');
Route::get('/e-stamps', function() {
    $user = auth()->user();
    $stamps = $user->stamps()->with('establishment')->get();
    $favorites = $user->favorites()->with('establishment')->get();
    $reviews = $user->reviews()->with('establishment')->get();
    return view('e-stamps', compact('stamps', 'favorites', 'reviews'));
})->middleware('auth')->name('e-stamps');


// Public QR code processing (for mobile apps/external scanners)
Route::get('/stamp/process/{establishmentId}', [StampController::class, 'showQRCodeScan'])->name('stamp.scan');
Route::get('/qr-scanner', function() {
    return view('qr-scanner');
})->name('qr.scanner');

// Public favorites check route (no auth required)
Route::get('/favorites/{establishmentId}/check', [FavoriteController::class, 'check'])->name('favorites.check');

// Stamp system routes
Route::middleware('auth')->group(function () {
    Route::post('/stamp/process/{establishmentId}', [StampController::class, 'processQRCode'])->name('stamp.process');
    Route::post('/stamp/test/{establishmentId}', [StampController::class, 'testStampCollection'])->name('stamp.test');
    Route::get('/stamps', [StampController::class, 'getUserStamps'])->name('stamps.user');
    Route::get('/qr-code/{establishmentId}', [StampController::class, 'getQRCode'])->name('qr-code.get');
    Route::get('/test-qr-codes', [StampController::class, 'showTestPage'])->name('test.qr-codes');
    Route::post('/stamp/generate-qr/{establishmentId}', [StampController::class, 'generateQRCode'])->name('stamp.generate-qr');
    Route::post('/stamp/regenerate-qr/{establishmentId}', [StampController::class, 'regenerateQRCode'])->name('stamp.regenerate-qr');
    
    // Favorites routes
    Route::post('/favorites/{establishmentId}/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::get('/favorites', [FavoriteController::class, 'getUserFavorites'])->name('favorites.user');
    
    // User settings routes
    Route::get('/settings', [App\Http\Controllers\UserController::class, 'showSettings'])->name('settings');
    Route::put('/settings/profile', [App\Http\Controllers\UserController::class, 'updateProfile'])->name('settings.profile');
    Route::put('/settings/password', [App\Http\Controllers\UserController::class, 'updatePassword'])->name('settings.password');
});

// Admin-only
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dash', [App\Http\Controllers\AdminEstablishmentController::class, 'dashboard'])->name('dash');
    Route::view('/reports', 'admin.reports')->name('reports');

    // Reports data (JSON) and CSV export
    Route::get('/reports/data', [App\Http\Controllers\AdminEstablishmentController::class, 'reports'])->name('reports.data');
    Route::get('/reports/export', [App\Http\Controllers\AdminEstablishmentController::class, 'exportReportsCsv'])->name('reports.export');

    Route::get('/manage', [App\Http\Controllers\AdminEstablishmentController::class, 'index'])->name('manage');
    Route::put('/establishment', [App\Http\Controllers\AdminEstablishmentController::class, 'update'])->name('establishment.update');
    Route::delete('/establishment-pictures/{picture}', [App\Http\Controllers\AdminEstablishmentController::class, 'deletePicture'])->name('establishment-pictures.destroy');
    Route::post('/log-guest', [App\Http\Controllers\AdminEstablishmentController::class, 'logGuest'])->name('log-guest');
    Route::get('/visitor-data', [App\Http\Controllers\AdminEstablishmentController::class, 'getVisitorData'])->name('visitor-data');
    Route::view('/settings', 'admin.settings')->name('settings');
});

// Superadmin-only
Route::middleware(['auth', 'role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\SuperAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/manage-rewards', [App\Http\Controllers\SuperAdminController::class, 'manageRewards'])->name('manage-rewards');
    Route::post('/send-reward-notifications', [App\Http\Controllers\SuperAdminController::class, 'sendRewardNotifications'])->name('send-reward-notifications');
    Route::post('/preview-email', [App\Http\Controllers\SuperAdminController::class, 'previewEmail'])->name('preview-email');
    Route::get('/manage-establishments', [App\Http\Controllers\EstablishmentController::class, 'index'])->name('manage-establishments');
    Route::post('/establishments', [App\Http\Controllers\EstablishmentController::class, 'store'])->name('establishments.store');
    Route::get('/establishments/{establishment}', [App\Http\Controllers\EstablishmentController::class, 'show'])->name('establishments.show');
    Route::put('/establishments/{establishment}', [App\Http\Controllers\EstablishmentController::class, 'update'])->name('establishments.update');
    Route::delete('/establishments/{establishment}', [App\Http\Controllers\EstablishmentController::class, 'destroy'])->name('establishments.destroy');
                    Route::delete('/establishment-pictures/{picture}', [App\Http\Controllers\EstablishmentController::class, 'deletePicture'])->name('establishment-pictures.destroy');
                Route::post('/users', [App\Http\Controllers\UserController::class, 'store'])->name('users.store');
                Route::get('/manage-admins', [App\Http\Controllers\AdminController::class, 'index'])->name('manage-admins');
                Route::get('/admins/{admin}', [App\Http\Controllers\AdminController::class, 'show'])->name('admins.show');
                Route::put('/admins/{admin}', [App\Http\Controllers\AdminController::class, 'update'])->name('admins.update');
                Route::delete('/admins/{admin}', [App\Http\Controllers\AdminController::class, 'destroy'])->name('admins.destroy');
                Route::view('/settings', 'superadmin.settings')->name('settings');
                Route::post('/change-password', [App\Http\Controllers\AdminController::class, 'changePassword'])->name('change-password');
                Route::post('/update-email', [App\Http\Controllers\SuperAdminController::class, 'updateEmail'])->name('update-email');
                
                // Tourist management routes
                Route::get('/manage-tourists', [TouristController::class, 'index'])->name('manage-tourists');
                Route::get('/tourists/{touristId}/stamps', [TouristController::class, 'getTouristStamps'])->name('get-tourist-stamps');
                Route::delete('/tourists/stamps/{stampId}', [TouristController::class, 'deleteStamp'])->name('delete-stamp');
                Route::delete('/tourists/{touristId}/stamps', [TouristController::class, 'deleteAllStamps'])->name('delete-all-stamps');
                Route::post('/logout', function() {
                    auth()->logout();
                    return redirect('/');
                })->name('logout');
});


