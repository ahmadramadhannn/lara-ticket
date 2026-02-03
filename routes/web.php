<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Operator\OperatorRegistrationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SuperAdmin\SuperAdminController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Language switch
Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

// Landing page / Schedule search
Route::get('/', [ScheduleController::class, 'index'])->name('home');
Route::get('/schedules', [ScheduleController::class, 'search'])->name('schedules.search');
Route::get('/schedules/{schedule}', [ScheduleController::class, 'show'])->name('schedules.show');

/*
|--------------------------------------------------------------------------
| Operator Registration Routes (Guests)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->prefix('operator')->group(function () {
    Route::get('/register', [OperatorRegistrationController::class, 'create'])->name('operator.register');
    Route::post('/register', [OperatorRegistrationController::class, 'store'])->name('operator.register.store');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Buyers)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    // Profile (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Booking
    Route::get('/booking/{schedule}', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/booking/{schedule}', [BookingController::class, 'store'])->name('booking.store');

    // Payments
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::post('/payments/{payment}/process', [PaymentController::class, 'process'])->name('payments.process');
    Route::get('/payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');

    // Tickets
    Route::get('/my-tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::get('/tickets/{ticket}/download', [TicketController::class, 'download'])->name('tickets.download');
});

/*
|--------------------------------------------------------------------------
| Operator Routes (Pending - Show waiting page)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('operator')->group(function () {
    Route::get('/pending', [OperatorRegistrationController::class, 'pending'])->name('operator.pending');
});

/*
|--------------------------------------------------------------------------
| Operator Routes (Approved operators only)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:operator', 'operator.approved'])->prefix('operator')->name('operator.')->group(function () {
    Route::get('/dashboard', function () {
        return view('operator.dashboard');
    })->name('dashboard');
    // TODO: Add schedule and bus management routes
});

/*
|--------------------------------------------------------------------------
| Super Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/registrations', [SuperAdminController::class, 'registrations'])->name('registrations');
    Route::get('/registrations/{operator}', [SuperAdminController::class, 'showRegistration'])->name('registrations.show');
    Route::post('/registrations/{operator}/approve', [SuperAdminController::class, 'approve'])->name('approve');
    Route::post('/registrations/{operator}/reject', [SuperAdminController::class, 'reject'])->name('reject');
    
    // Terminals CRUD - TODO
    Route::get('/terminals', function () {
        return view('super-admin.terminals.index');
    })->name('terminals.index');
    
    // Operators CRUD - TODO
    Route::get('/operators', function () {
        return view('super-admin.operators.index');
    })->name('operators.index');
});

/*
|--------------------------------------------------------------------------
| Verifier Routes (Operators can verify tickets)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:operator,super_admin'])->prefix('ticket-check')->group(function () {
    Route::get('/', [VerificationController::class, 'index'])->name('ticket-check.index');
    Route::post('/', [VerificationController::class, 'verify'])->name('ticket-check.verify');
    Route::post('/mark-used/{ticket}', [VerificationController::class, 'markUsed'])->name('ticket-check.markUsed');
});

/*
|--------------------------------------------------------------------------
| API Routes for Verification (can be used by mobile apps)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'role:operator,super_admin'])->prefix('api')->group(function () {
    Route::post('/verify-ticket', [VerificationController::class, 'apiVerify']);
});

/*
|--------------------------------------------------------------------------
| Auth Routes (from Breeze)
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
