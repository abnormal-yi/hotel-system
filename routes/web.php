<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CCTVController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EFDController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\FeatureFlagController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\HousekeepingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PublicBookingController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\SmartKeyController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\SyncQueueController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public booking portal
Route::get('/booking', [PublicBookingController::class, 'index'])->name('public-booking.index');
Route::post('/booking/check-availability', [PublicBookingController::class, 'checkAvailability'])->name('public-booking.availability');
Route::post('/booking/store', [PublicBookingController::class, 'store'])->name('public-booking.store');
Route::get('/booking/success/{booking}', [PublicBookingController::class, 'success'])->name('public-booking.success');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/search', [DashboardController::class, 'search'])->name('dashboard.search');

    Route::get('/bookings/calendar', [BookingController::class, 'calendar'])->name('bookings.calendar');
    Route::get('/bookings/availability', [BookingController::class, 'availability'])->name('bookings.availability');
    Route::patch('/bookings/{booking}/checkin', [BookingController::class, 'checkin'])->name('bookings.checkin');
    Route::patch('/bookings/{booking}/checkout', [BookingController::class, 'checkout'])->name('bookings.checkout');
    Route::patch('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::resource('bookings', BookingController::class);

    Route::resource('guests', GuestController::class);
    Route::patch('guests/{guest}/blacklist', [GuestController::class, 'blacklist'])->name('guests.blacklist');

    Route::get('/rooms/availability', [RoomController::class, 'availability'])->name('rooms.availability');
    Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
    Route::get('/rooms/create', [RoomController::class, 'create'])->name('rooms.create')->middleware('role:creator,manager');
    Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store')->middleware('role:creator,manager');
    Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
    Route::get('/rooms/{room}/edit', [RoomController::class, 'edit'])->name('rooms.edit');
    Route::put('/rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
    Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy')->middleware('role:creator,manager');

    Route::get('/payments/create/{booking}', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments/store/{booking}', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{payment}/invoice', [PaymentController::class, 'invoice'])->name('payments.invoice');
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::get('/payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit')->middleware('role:creator,manager');
    Route::put('/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update')->middleware('role:creator,manager');
    Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy')->middleware('role:creator,manager');

    Route::get('/housekeeping', [HousekeepingController::class, 'index'])->name('housekeeping.index');
    Route::get('/housekeeping/create', [HousekeepingController::class, 'create'])->name('housekeeping.create')->middleware('role:creator,manager,receptionist');
    Route::post('/housekeeping', [HousekeepingController::class, 'store'])->name('housekeeping.store')->middleware('role:creator,manager,receptionist');
    Route::get('/housekeeping/{housekeepingTask}', [HousekeepingController::class, 'show'])->name('housekeeping.show');
    Route::get('/housekeeping/{housekeepingTask}/edit', [HousekeepingController::class, 'edit'])->name('housekeeping.edit')->middleware('role:creator,manager');
    Route::put('/housekeeping/{housekeepingTask}', [HousekeepingController::class, 'update'])->name('housekeeping.update')->middleware('role:creator,manager');
    Route::delete('/housekeeping/{housekeepingTask}', [HousekeepingController::class, 'destroy'])->name('housekeeping.destroy')->middleware('role:creator,manager');



    Route::get('/feature-flags', [FeatureFlagController::class, 'index'])->name('feature-flags.index')->middleware('role:creator');
    Route::post('/feature-flags/{id}/toggle', [FeatureFlagController::class, 'toggle'])->name('feature-flags.toggle')->middleware('role:creator');

    Route::get('/settings', [SystemController::class, 'settings'])->name('settings.index')->middleware('role:creator');
    Route::post('/settings', [SystemController::class, 'updateSettings'])->name('settings.update')->middleware('role:creator');

    Route::get('/activity-log', [SystemController::class, 'activityLog'])->name('activity-log.index')->middleware('role:creator,manager');

    Route::resource('users', UserController::class)->middleware('role:creator,manager');

    Route::resource('room-types', RoomTypeController::class)->middleware('role:creator,manager');
    Route::resource('facilities', FacilityController::class)->middleware('role:creator');

    Route::get('/charts', [ChartController::class, 'index'])->name('charts.index');
    Route::get('/charts/pdf', [ChartController::class, 'pdf'])->name('charts.pdf');

    Route::get('/efd', [EFDController::class, 'index'])->name('efd.index');
    Route::get('/efd/from-payment/{paymentId}', [EFDController::class, 'fromPayment'])->name('efd.from-payment');
    Route::get('/efd/{id}', [EFDController::class, 'show'])->name('efd.show');
    Route::get('/efd/{id}/receipt', [EFDController::class, 'receipt'])->name('efd.receipt');

    Route::get('/sync-queue', [SyncQueueController::class, 'index'])->name('sync-queue.index')->middleware('role:creator');
    Route::post('/sync-queue/{id}/retry', [SyncQueueController::class, 'retry'])->name('sync-queue.retry')->middleware('role:creator');
    Route::post('/sync-queue/clear', [SyncQueueController::class, 'clearCompleted'])->name('sync-queue.clear')->middleware('role:creator');

    Route::get('/smart-keys', [SmartKeyController::class, 'index'])->name('smart-keys.index')->middleware('role:creator,manager,receptionist');
    Route::get('/smart-keys/create', [SmartKeyController::class, 'create'])->name('smart-keys.create')->middleware('role:creator,manager,receptionist');
    Route::post('/smart-keys', [SmartKeyController::class, 'store'])->name('smart-keys.store')->middleware('role:creator,manager,receptionist');
    Route::post('/smart-keys/{id}/toggle', [SmartKeyController::class, 'toggle'])->name('smart-keys.toggle')->middleware('role:creator,manager,receptionist');

    Route::resource('cctv', CCTVController::class)->middleware('role:creator,manager');

    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index')->middleware('role:creator,manager');
    Route::get('/inventory/create', [InventoryController::class, 'create'])->name('inventory.create')->middleware('role:creator,manager');
    Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store')->middleware('role:creator,manager');
    Route::get('/inventory/{id}/edit', [InventoryController::class, 'edit'])->name('inventory.edit')->middleware('role:creator,manager');
    Route::put('/inventory/{id}', [InventoryController::class, 'update'])->name('inventory.update')->middleware('role:creator,manager');
    Route::delete('/inventory/{id}', [InventoryController::class, 'destroy'])->name('inventory.destroy')->middleware('role:creator,manager');

    Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index')->middleware('role:creator,manager');
    Route::get('/maintenance/create', [MaintenanceController::class, 'create'])->name('maintenance.create')->middleware('role:creator,manager');
    Route::post('/maintenance', [MaintenanceController::class, 'store'])->name('maintenance.store')->middleware('role:creator,manager');
    Route::get('/maintenance/{id}', [MaintenanceController::class, 'show'])->name('maintenance.show')->middleware('role:creator,manager');
    Route::get('/maintenance/{id}/edit', [MaintenanceController::class, 'edit'])->name('maintenance.edit')->middleware('role:creator,manager');
    Route::put('/maintenance/{id}', [MaintenanceController::class, 'update'])->name('maintenance.update')->middleware('role:creator,manager');
    Route::delete('/maintenance/{id}', [MaintenanceController::class, 'destroy'])->name('maintenance.destroy')->middleware('role:creator,manager');

    Route::get('/pos', [POSController::class, 'index'])->name('pos.index')->middleware('role:creator,manager,receptionist');
    Route::get('/pos/create', [POSController::class, 'create'])->name('pos.create')->middleware('role:creator,manager,receptionist');
    Route::get('/pos/room-order', [POSController::class, 'roomOrderCreate'])->name('pos.room-order')->middleware('role:creator,manager,receptionist');
    Route::post('/pos/room-order', [POSController::class, 'roomOrderStore'])->name('pos.room-order.store')->middleware('role:creator,manager,receptionist');
    Route::post('/pos', [POSController::class, 'store'])->name('pos.store')->middleware('role:creator,manager,receptionist');
    Route::get('/pos/{id}', [POSController::class, 'show'])->name('pos.show')->middleware('role:creator,manager,receptionist');
    Route::post('/pos/{id}/complete', [POSController::class, 'complete'])->name('pos.complete')->middleware('role:creator,manager,receptionist');
});

// TEMPORARY: Run migrations after upload (REMOVE after use!)
Route::get('/setup-migrate', function () {
    Artisan::call('migrate --seed --force');
    return '<pre>' . Artisan::output() . '</pre>';
});

// TEMPORARY: Create storage link (REMOVE after use!)
Route::get('/setup-storage', function () {
    Artisan::call('storage:link');
    return '<pre>' . Artisan::output() . '</pre>';
});

Route::redirect('/', '/login');
