<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceRequestController;

Route::get('/', function () {
    return view('welcome');
});

// Trash and restore routes for ServiceRequest recycle bin
Route::get('service-requests/trash', [ServiceRequestController::class, 'trash'])->name('service-requests.trash');
Route::get('service-requests/{id}/trashed', [ServiceRequestController::class, 'showTrashed'])->name('service-requests.showTrashed');
Route::post('service-requests/{id}/restore', [ServiceRequestController::class, 'restore'])->name('service-requests.restore');
Route::delete('service-requests/{id}/force-delete', [ServiceRequestController::class, 'forceDelete'])->name('service-requests.forceDelete');

// Resource routes for service requests
Route::resource('service-requests', ServiceRequestController::class);
