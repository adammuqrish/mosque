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

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [DashboardController::class, 'index'])->middleware('auth');

// Route::get('/', function () {
//     return view('welcome');
// });

// Login Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Donation Routes (Hanya Admin patut guna, tapi kita buka dulu untuk test)
Route::get('/donations', [DonationController::class, 'index'])->name('donations.index');
Route::post('/donations', [DonationController::class, 'store'])->name('donations.store');

// Withdrawal Routes
Route::get('/withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals.index');
Route::post('/withdrawals', [WithdrawalController::class, 'store'])->name('withdrawals.store');

// Routes for Approve/Reject (Perlu ID)
Route::post('/withdrawals/{id}/approve', [WithdrawalController::class, 'approve'])->name('withdrawals.approve');
Route::post('/withdrawals/{id}/reject', [WithdrawalController::class, 'reject'])->name('withdrawals.reject');

// Volunteer Routes
Route::post('/volunteer/profile/update', [VolunteerController::class, 'updateProfile'])->name('volunteer.update');
Route::post('/events/{id}/join', [VolunteerController::class, 'joinEvent'])->name('volunteer.join');
Route::get('/volunteer/my-events', [VolunteerController::class, 'myEvents'])->name('volunteer.my-events');

// Transparency Routes
Route::get('/transparency', [VolunteerController::class, 'transparency'])->name('transparency.index');

// Event Management (Admin Only)
Route::get('/events/manage', [EventController::class, 'index'])->name('events.manage');
Route::post('/events', [EventController::class, 'store'])->name('events.store');
Route::delete('/events/{id}', [EventController::class, 'destroy'])->name('events.destroy');

// Financial Reports
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

// Register Routes
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Profile Routes
Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
Route::post('/profile/update-info', [ProfileController::class, 'updateInfo'])->name('profile.update.info');
Route::post('/profile/update-skills', [ProfileController::class, 'updateSkills'])->name('profile.update.skills');
Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update.password');
