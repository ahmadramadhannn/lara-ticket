<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Operator\BusController;
use App\Http\Controllers\Operator\OperatorRegistrationController;
use App\Http\Controllers\Operator\ScheduleController as OperatorScheduleController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SuperAdmin\RouteController;
use App\Http\Controllers\SuperAdmin\SuperAdminController;
use App\Http\Controllers\SuperAdmin\TerminalController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Vercel Migration Route (Hit this once after deploy)
Route::get('/migrate', function () {
    if (app()->environment('production')) {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        return "Migration successful: " . \Illuminate\Support\Facades\Artisan::output();
    }
    return "Not in production";
});

// Language switch
Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

// Landing page / Schedule search
Route::get('/', [ScheduleController::class, 'index'])->name('home');
Route::get('/schedules', [ScheduleController::class, 'search'])->name('schedules.search');
Route::get('/schedules/{schedule}', [ScheduleController::class, 'show'])->name('schedules.show');
Route::get('/api/destinations/{terminal}', [ScheduleController::class, 'getDestinations'])->name('api.destinations');

/*
|--------------------------------------------------------------------------
| Operator Registration Routes (Guests)
|--------------------------------------------------------------------------
*/

Route::middleware(['guest', 'throttle:3,60'])->prefix('operator')->group(function () {
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

/*
|--------------------------------------------------------------------------
| Operator Routes - LEGACY (Now handled by Filament at /operator)
|--------------------------------------------------------------------------
| These routes are commented out as Filament now handles the operator dashboard.
| The old Blade views are kept for reference but Filament provides a better UI.
*/

// Route::middleware(['auth', 'role:operator', 'operator.approved'])->prefix('operator')->name('operator.')->group(function () {
//     Route::get('/dashboard', function () {
//         return view('operator.dashboard');
//     })->name('dashboard');
//     
//     // Bus Management
//     Route::resource('buses', BusController::class)->except(['show']);
//     
//     // Schedule Management
//     Route::resource('schedules', OperatorScheduleController::class)->except(['show']);
//     Route::post('/schedules/bulk-create', [OperatorScheduleController::class, 'bulkCreate'])->name('schedules.bulk-create');
// });

/*
|--------------------------------------------------------------------------
| Super Admin Routes - LEGACY (Now handled by Filament at /super-admin)
|--------------------------------------------------------------------------
| These routes are commented out as Filament now handles the super admin dashboard.
*/

// Route::middleware(['auth', 'role:super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
//     Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
//     
//     // Operator Registrations
//     Route::get('/registrations', [SuperAdminController::class, 'registrations'])->name('registrations');
//     Route::get('/registrations/{operator}', [SuperAdminController::class, 'showRegistration'])->name('registrations.show');
//     Route::post('/registrations/{operator}/approve', [SuperAdminController::class, 'approve'])->name('approve');
//     Route::post('/registrations/{operator}/reject', [SuperAdminController::class, 'reject'])->name('reject');
//     
//     // Terminals CRUD
//     Route::resource('terminals', TerminalController::class)->except(['show']);
//     
//     // Routes CRUD
//     Route::resource('routes', RouteController::class)->except(['show']);
//     
//     // Operators List (view only)
//     Route::get('/operators', [SuperAdminController::class, 'operators'])->name('operators.index');
// });

/*
|--------------------------------------------------------------------------
| Verifier Routes (Company admins and terminal admins can verify tickets)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:company_admin,terminal_admin,super_admin'])->prefix('ticket-check')->group(function () {
    Route::get('/', [VerificationController::class, 'index'])->name('ticket-check.index');
    Route::post('/', [VerificationController::class, 'verify'])->name('ticket-check.verify');
    Route::post('/mark-used/{ticket}', [VerificationController::class, 'markUsed'])->name('ticket-check.markUsed');
});

/*
|--------------------------------------------------------------------------
| API Routes for Verification (can be used by mobile apps)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'role:company_admin,terminal_admin,super_admin'])->prefix('api')->group(function () {
    Route::post('/verify-ticket', [VerificationController::class, 'apiVerify']);
});

/*
|--------------------------------------------------------------------------
| Auth Routes (from Breeze)
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
