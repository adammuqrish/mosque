<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\VolunteerController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\GamificationController;
use App\Http\Controllers\Admin\GamificationAdminController;
use App\Http\Controllers\LandingController;

// Landing page — guests see landing, authenticated users go to dashboard
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return app(LandingController::class)->index();
})->name('landing');

// Dashboard for authenticated users
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

Route::get('/login', [AuthController::class, 'showLoginForm'])->middleware('guest')->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware(['guest', 'throttle:5,1'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->middleware('guest')->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware(['guest', 'throttle:10,1'])->name('register');

// Email Verification Routes
Route::get('/email/verify', [AuthController::class, 'showVerifyNotice'])->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])->middleware('signed')->name('verification.verify');
Route::get('/email/resend', [AuthController::class, 'showResendForm'])->middleware('guest')->name('verification.resend.form');
Route::post('/email/resend', [AuthController::class, 'resendVerification'])->middleware(['throttle:3,1'])->name('verification.resend');

// Password Reset Routes
Route::get('/password/reset', [AuthController::class, 'showLinkRequestForm'])->middleware('guest')->name('password.request');
Route::post('/password/email', [AuthController::class, 'sendResetLinkEmail'])->middleware(['guest', 'throttle:3,1'])->name('password.email');
Route::get('/password/reset/{token}', [AuthController::class, 'showResetForm'])->middleware('guest')->name('password.reset');
Route::post('/password/reset', [AuthController::class, 'reset'])->middleware(['guest', 'throttle:3,1'])->name('password.update');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/update-info', [ProfileController::class, 'updateInfo'])->name('profile.update.info');
    Route::post('/profile/update-skills', [ProfileController::class, 'updateSkills'])->name('profile.update.skills');
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update.password');
    // STEP 1: Add route for referral code generation (AJAX endpoint)
    Route::post('/profile/referral/generate', [ProfileController::class, 'generateReferralCode'])
        ->name('profile.referral.generate');

    Route::post('/profile/update-avatar', [ProfileController::class, 'updateAvatar'])->name('profile.update.avatar');
    Route::delete('/profile/delete-avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.delete.avatar');

    Route::get('/transparency', [VolunteerController::class, 'transparency'])->name('transparency.index');

    Route::get('/volunteer/my-events', [VolunteerController::class, 'myEvents'])->name('volunteer.my-events');
    Route::post('/volunteer/profile/update', [VolunteerController::class, 'updateProfile'])->name('volunteer.update');
    Route::post('/events/{id}/join', [VolunteerController::class, 'joinEvent'])->name('volunteer.join');
    Route::delete('/events/{id}/leave', [VolunteerController::class, 'leaveEvent'])->name('volunteer.leave');

    // Notification Routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markRead'])->name('notifications.mark-read');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/donations', [DonationController::class, 'index'])->name('donations.index');
    Route::post('/donations', [DonationController::class, 'store'])->name('donations.store');
    Route::get('/donations/batch', [DonationController::class, 'batchForm'])->name('donations.batch.form');
    Route::post('/donations/batch', [DonationController::class, 'batchStore'])->name('donations.batch.store');
    Route::get('/donations/fund-purposes', [DonationController::class, 'fundPurposeIndex'])->name('donations.fund-purposes');
    Route::post('/donations/fund-purposes', [DonationController::class, 'fundPurposeStore'])->name('donations.fund-purposes.store');
    Route::put('/donations/fund-purposes/{fundPurpose}', [DonationController::class, 'fundPurposeUpdate'])->name('donations.fund-purposes.update');
    Route::delete('/donations/fund-purposes/{fundPurpose}', [DonationController::class, 'fundPurposeDestroy'])->name('donations.fund-purposes.destroy');
    Route::patch('/donations/{id}/confirm', [DonationController::class, 'confirm'])->name('donations.confirm');
    Route::patch('/donations/{id}/dispute', [DonationController::class, 'dispute'])->name('donations.dispute');
    Route::get('/donations/{id}/akad/print', [DonationController::class, 'printAkad'])->name('donations.akad.print');

    Route::post('/withdrawals', [WithdrawalController::class, 'store'])->name('withdrawals.store');

    // Event Management Routes
    Route::get('/events/manage', [EventController::class, 'index'])->name('events.manage');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/events/{id}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{id}', [EventController::class, 'update'])->name('events.update');
    Route::patch('/events/{id}/status', [EventController::class, 'changeStatus'])->name('events.changeStatus');
    Route::delete('/events/{id}', [EventController::class, 'destroy'])->name('events.destroy');
    
    // Volunteer Management
    Route::get('/events/{id}/volunteers', [EventController::class, 'volunteers'])->name('events.volunteers');
    Route::delete('/events/{eventId}/volunteers/{userId}', [EventController::class, 'removeVolunteer'])->name('events.volunteers.remove');
    
    // Attendance Management
    Route::patch('/events/{eventId}/attendance/{userId}', [EventController::class, 'updateAttendance'])->name('events.attendance.update');
    Route::post('/events/{eventId}/attendance/bulk-approve', [EventController::class, 'bulkApproveAttendance'])->name('events.attendance.bulk-approve');
    Route::post('/events/{eventId}/attendance/bulk-absent', [EventController::class, 'bulkMarkAbsent'])->name('events.attendance.bulk-absent');
});

Route::middleware(['auth', 'role:admin,treasurer'])->group(function () {
    Route::get('/withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    
    // Export Routes - CSV
    Route::get('/reports/export/donations/csv', [ReportController::class, 'exportDonationsCSV'])->name('reports.export.donations.csv');
    Route::get('/reports/export/events/csv', [ReportController::class, 'exportEventsCSV'])->name('reports.export.events.csv');
    Route::get('/reports/export/attendance/csv', [ReportController::class, 'exportAttendanceCSV'])->name('reports.export.attendance.csv');
    Route::get('/reports/export/financial/csv', [ReportController::class, 'exportFinancialCSV'])->name('reports.export.financial.csv');
    Route::get('/reports/export/gamification/csv', [ReportController::class, 'exportGamificationCSV'])->name('reports.export.gamification.csv');

    // Export Routes - PDF
    Route::get('/reports/export/donations/pdf', [ReportController::class, 'exportDonationsPDF'])->name('reports.export.donations.pdf');
    Route::get('/reports/export/events/pdf', [ReportController::class, 'exportEventsPDF'])->name('reports.export.events.pdf');
    Route::get('/reports/export/attendance/pdf', [ReportController::class, 'exportAttendancePDF'])->name('reports.export.attendance.pdf');
    Route::get('/reports/export/financial/pdf', [ReportController::class, 'exportFinancialPDF'])->name('reports.export.financial.pdf');
    Route::get('/reports/export/gamification/pdf', [ReportController::class, 'exportGamificationPDF'])->name('reports.export.gamification.pdf');
});

Route::middleware(['auth', 'role:treasurer'])->group(function () {
    Route::post('/withdrawals/{id}/approve', [WithdrawalController::class, 'approve'])->name('withdrawals.approve');
    Route::post('/withdrawals/{id}/reject', [WithdrawalController::class, 'reject'])->name('withdrawals.reject');
});

// Gamification Routes (All authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::prefix('gamification')->name('gamification.')->group(function () {
        Route::get('/dashboard', [GamificationController::class, 'dashboard'])->name('dashboard');
        Route::get('/points-history', [GamificationController::class, 'pointsHistory'])->name('points-history');
        Route::get('/badges', [GamificationController::class, 'badges'])->name('badges');
        Route::get('/rewards', [GamificationController::class, 'rewards'])->name('rewards');
        Route::post('/rewards/{reward}/redeem', [GamificationController::class, 'redeem'])->name('redeem');
        Route::get('/leaderboard', [GamificationController::class, 'leaderboard'])->name('leaderboard');
        Route::get('/my-redemptions', [GamificationController::class, 'myRedemptions'])->name('my-redemptions');
        Route::get('/certificate/{redemption}/download', [GamificationController::class, 'downloadCertificate'])->name('certificate.download');
    });
});

// Admin Gamification Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin/gamification')->name('admin.gamification.')->group(function () {
    Route::get('/', [GamificationAdminController::class, 'index'])->name('index');
    Route::post('/members/{user}/adjust', [GamificationAdminController::class, 'adjustPoints'])->name('adjust');
    Route::get('/members/{user}/transactions', [GamificationAdminController::class, 'viewTransactions'])->name('transactions');
    Route::get('/redemptions', [GamificationAdminController::class, 'pendingRedemptions'])->name('redemptions');
    Route::post('/redemptions/{redemption}/fulfill', [GamificationAdminController::class, 'fulfillRedemption'])->name('fulfill');

    // Badge Management
    Route::get('/badges', [GamificationAdminController::class, 'badgesIndex'])->name('badges.index');
    Route::get('/badges/create', [GamificationAdminController::class, 'createBadge'])->name('badges.create');
    Route::post('/badges', [GamificationAdminController::class, 'storeBadge'])->name('badges.store');
    Route::get('/badges/{badge}/edit', [GamificationAdminController::class, 'editBadge'])->name('badges.edit');
    Route::put('/badges/{badge}', [GamificationAdminController::class, 'updateBadge'])->name('badges.update');
    Route::patch('/badges/{badge}/toggle', [GamificationAdminController::class, 'toggleBadge'])->name('badges.toggle');
    Route::delete('/badges/{badge}', [GamificationAdminController::class, 'destroyBadge'])->name('badges.destroy');

    // Reward Management
    Route::get('/rewards', [GamificationAdminController::class, 'rewardsIndex'])->name('rewards.index');
    Route::get('/rewards/create', [GamificationAdminController::class, 'createReward'])->name('rewards.create');
    Route::post('/rewards', [GamificationAdminController::class, 'storeReward'])->name('rewards.store');
    Route::get('/rewards/{reward}/edit', [GamificationAdminController::class, 'editReward'])->name('rewards.edit');
    Route::put('/rewards/{reward}', [GamificationAdminController::class, 'updateReward'])->name('rewards.update');
    Route::patch('/rewards/{reward}/toggle', [GamificationAdminController::class, 'toggleReward'])->name('rewards.toggle');
    Route::delete('/rewards/{reward}', [GamificationAdminController::class, 'destroyReward'])->name('rewards.destroy');

    // Tier Milestone Management
    Route::get('/tiers', [GamificationAdminController::class, 'tiersIndex'])->name('tiers.index');
    Route::get('/tiers/create', [GamificationAdminController::class, 'createTier'])->name('tiers.create');
    Route::post('/tiers', [GamificationAdminController::class, 'storeTier'])->name('tiers.store');
    Route::get('/tiers/{tier}/edit', [GamificationAdminController::class, 'editTier'])->name('tiers.edit');
    Route::put('/tiers/{tier}', [GamificationAdminController::class, 'updateTier'])->name('tiers.update');
    Route::delete('/tiers/{tier}', [GamificationAdminController::class, 'destroyTier'])->name('tiers.destroy');
});
