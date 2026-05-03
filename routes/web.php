<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceRequestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// after login, redirect to service requests list
Route::get('/dashboard', function () {
    return redirect()->route('service-requests.index');
})->middleware(['auth', 'verified'])->name('dashboard');

// Auth-protected routes: Service requests and profile
Route::middleware('auth')->group(function () {
    // profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ServiceRequest recycle bin routes
    Route::get('service-requests/trash', [ServiceRequestController::class, 'trash'])->name('service-requests.trash');
    Route::get('service-requests/{id}/trashed', [ServiceRequestController::class, 'showTrashed'])->name('service-requests.showTrashed');
    Route::post('service-requests/{id}/restore', [ServiceRequestController::class, 'restore'])->name('service-requests.restore');
    Route::delete('service-requests/{id}/force-delete', [ServiceRequestController::class, 'forceDelete'])->name('service-requests.forceDelete');

    // Resource routes for service requests
    Route::resource('service-requests', ServiceRequestController::class);
});

require __DIR__.'/auth.php';
