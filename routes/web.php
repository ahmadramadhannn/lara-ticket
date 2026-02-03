<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Landing page / Schedule search
Route::get('/', [ScheduleController::class, 'index'])->name('home');
Route::get('/schedules', [ScheduleController::class, 'search'])->name('schedules.search');
Route::get('/schedules/{schedule}', [ScheduleController::class, 'show'])->name('schedules.show');

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
| Verifier Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:verifier,admin'])->prefix('ticket-check')->group(function () {
    Route::get('/', [VerificationController::class, 'index'])->name('ticket-check.index');
    Route::post('/', [VerificationController::class, 'verify'])->name('ticket-check.verify');
    Route::post('/mark-used/{ticket}', [VerificationController::class, 'markUsed'])->name('ticket-check.markUsed');
});

/*
|--------------------------------------------------------------------------
| API Routes for Verification (can be used by mobile apps)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'role:verifier,admin'])->prefix('api')->group(function () {
    Route::post('/verify-ticket', [VerificationController::class, 'apiVerify']);
});

/*
|--------------------------------------------------------------------------
| Auth Routes (from Breeze)
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
