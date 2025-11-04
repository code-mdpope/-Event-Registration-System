<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\RegistrationController as AdminRegistrationController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', [EventController::class, 'index'])->name('home');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

// Authentication routes (from Laravel Breeze)
require __DIR__.'/auth.php';

// Authenticated user routes
Route::middleware(['auth', 'verified'])->group(function () {
    // User Dashboard (redirect based on role)
    Route::get('/dashboard', function () {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('user.dashboard');
    })->name('dashboard');
    
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    
    // Registration
    Route::post('/events/{event}/register', [RegistrationController::class, 'store'])->name('registrations.store');
    Route::post('/registrations/{registration}/cancel', [RegistrationController::class, 'cancel'])->name('registrations.cancel');
    
    // QR Code ticket
    Route::get('/registrations/{registration}/ticket', [ExportController::class, 'generateQRCode'])->name('registrations.ticket');
    
    // Profile management (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Events
    Route::resource('events', AdminEventController::class);
    
    // Users
    Route::resource('users', AdminUserController::class);
    Route::post('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
    
    // Registrations
    Route::get('/registrations', [AdminRegistrationController::class, 'index'])->name('registrations.index');
    Route::get('/registrations/{registration}', [AdminRegistrationController::class, 'show'])->name('registrations.show');
    Route::post('/registrations/{registration}/approve', [AdminRegistrationController::class, 'approve'])->name('registrations.approve');
    Route::post('/registrations/{registration}/decline', [AdminRegistrationController::class, 'decline'])->name('registrations.decline');
    Route::delete('/registrations/{registration}', [AdminRegistrationController::class, 'destroy'])->name('registrations.destroy');
    
    // Attendances
    Route::get('/attendances', [AdminAttendanceController::class, 'index'])->name('attendances.index');
    Route::get('/events/{event}/attendances/manage', [AdminAttendanceController::class, 'manage'])->name('attendances.manage');
    Route::post('/events/{event}/attendances/mark', [AdminAttendanceController::class, 'mark'])->name('attendances.mark');
    Route::delete('/events/{event}/attendances/{userId}/unmark', [AdminAttendanceController::class, 'unmark'])->name('attendances.unmark');
    Route::get('/events/{event}/attendances/statistics', [AdminAttendanceController::class, 'statistics'])->name('attendances.statistics');
    Route::delete('/attendances/{attendance}', [AdminAttendanceController::class, 'destroy'])->name('attendances.destroy');
    
    // Exports
    Route::get('/events/{event}/export/registrations/pdf', [ExportController::class, 'exportRegistrationsPDF'])->name('exports.registrations.pdf');
    Route::get('/events/{event}/export/registrations/csv', [ExportController::class, 'exportRegistrationsCSV'])->name('exports.registrations.csv');
    Route::get('/events/{event}/export/attendance/pdf', [ExportController::class, 'exportAttendancePDF'])->name('exports.attendance.pdf');
    Route::get('/events/{event}/export/attendance/csv', [ExportController::class, 'exportAttendanceCSV'])->name('exports.attendance.csv');
});
