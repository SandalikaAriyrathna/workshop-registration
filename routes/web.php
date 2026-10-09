
<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkshopController;
use Illuminate\Support\Facades\Route;

// Welcome Page
Route::get('/', function () {
    return view('welcome');
});

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

// Admin Only - User Management
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('users', UserController::class)
        ->only(['index', 'create', 'store']);
});

// Manager Only - Create and Edit Workshops
Route::middleware(['auth', 'role:manager'])->group(function () {

    Route::get('/workshops/create', [WorkshopController::class, 'create'])
        ->name('workshops.create');

    Route::post('/workshops', [WorkshopController::class, 'store'])
        ->name('workshops.store');

    Route::get('/workshops/{workshop}/edit', [WorkshopController::class, 'edit'])
        ->name('workshops.edit');

    Route::put('/workshops/{workshop}', [WorkshopController::class, 'update'])
        ->name('workshops.update');
});

// Manager and Staff - View Workshops
Route::middleware(['auth', 'role:manager,staff'])->group(function () {

    Route::get('/workshops', [WorkshopController::class, 'index'])
        ->name('workshops.index');

    Route::get('/workshops/{workshop}', [WorkshopController::class, 'show'])
        ->name('workshops.show');

    Route::patch('/registrations/{registration}/cancel',
        [RegistrationController::class, 'cancel'])
        ->name('registrations.cancel');
});

// Manager and Staff - Register attendees
Route::middleware(['auth', 'role:manager,staff'])->group(function () {

    Route::get('/workshops/{workshop}/register',
        [RegistrationController::class, 'create'])
        ->name('registrations.create');

    Route::post('/registrations',
        [RegistrationController::class, 'store'])
        ->name('registrations.store');

});

// Breeze Authentication Routes
require __DIR__.'/auth.php';
