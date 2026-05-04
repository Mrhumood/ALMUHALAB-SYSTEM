<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RequestServiceController;
use App\Http\Controllers\ServiceCatalogAdminController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\ServiceTypeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return redirect()->route('service-requests.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    // ── Profile ───────────────────────────────────────────
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Trash (before resource routes to avoid binding conflict) ──
    Route::get('service-requests/trash',
        [ServiceRequestController::class, 'trash'])
        ->middleware('permission:view_trash')
        ->name('service-requests.trash');

    Route::get('service-requests/{id}/trashed',
        [ServiceRequestController::class, 'showTrashed'])
        ->middleware('permission:view_trash')
        ->name('service-requests.showTrashed');

    Route::post('service-requests/{id}/restore',
        [ServiceRequestController::class, 'restore'])
        ->middleware('permission:restore_request')
        ->name('service-requests.restore');

    Route::delete('service-requests/{id}/force-delete',
        [ServiceRequestController::class, 'forceDelete'])
        ->middleware('permission:force_delete_request')
        ->name('service-requests.forceDelete');

    // ── Service Requests CRUD ─────────────────────────────
    Route::get('service-requests',
        [ServiceRequestController::class, 'index'])
        ->middleware('permission:view_request')
        ->name('service-requests.index');

    Route::get('service-requests/create',
        [ServiceRequestController::class, 'create'])
        ->middleware('permission:create_request')
        ->name('service-requests.create');

    Route::post('service-requests',
        [ServiceRequestController::class, 'store'])
        ->middleware('permission:create_request')
        ->name('service-requests.store');

    Route::get('service-requests/{service_request}',
        [ServiceRequestController::class, 'show'])
        ->middleware('permission:view_request')
        ->name('service-requests.show');

    Route::get('service-requests/{service_request}/edit',
        [ServiceRequestController::class, 'edit'])
        ->middleware('permission:edit_request')
        ->name('service-requests.edit');

    Route::put('service-requests/{service_request}',
        [ServiceRequestController::class, 'update'])
        ->middleware('permission:edit_request')
        ->name('service-requests.update');

    Route::patch('service-requests/{service_request}',
        [ServiceRequestController::class, 'update'])
        ->middleware('permission:edit_request')
        ->name('service-requests.update-patch');

    Route::delete('service-requests/{service_request}',
        [ServiceRequestController::class, 'destroy'])
        ->middleware('permission:delete_request')
        ->name('service-requests.destroy');

    Route::delete('service-requests/{service_request}/attachments/{attachment}',
        [ServiceRequestController::class, 'deleteAttachment'])
        ->middleware('permission:delete_request')
        ->name('service-requests.attachments.destroy');

    // ── Request Services ──────────────────────────────────
    Route::middleware('permission:manage_services')
        ->prefix('service-requests/{service_request}/services')
        ->name('request-services.')
        ->group(function () {
            Route::post('/',               [RequestServiceController::class, 'store'])  ->name('store');
            Route::put('/{requestService}',[RequestServiceController::class, 'update']) ->name('update');
            Route::delete('/{requestService}',[RequestServiceController::class, 'destroy'])->name('destroy');
        });

    // ── Follow-Ups (Timeline) ─────────────────────────────
    Route::middleware('permission:manage_followups')
        ->prefix('service-requests/{service_request}/follow-ups')
        ->name('follow-ups.')
        ->group(function () {
            Route::post('/',                        [FollowUpController::class, 'store'])  ->name('store');
            Route::get('/{followUp}/edit',          [FollowUpController::class, 'edit'])   ->name('edit');
            Route::put('/{followUp}',               [FollowUpController::class, 'update']) ->name('update');
            Route::delete('/{followUp}',            [FollowUpController::class, 'destroy'])->name('destroy');
            Route::patch('/{followUp}/toggle',      [FollowUpController::class, 'toggle']) ->name('toggle');
        });

    // ── Admin Panel ───────────────────────────────────────
    Route::middleware('permission:manage_users')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            Route::get('users',
                [AdminController::class, 'users'])
                ->name('users.index');

            Route::patch('users/{user}/role',
                [AdminController::class, 'updateUserRole'])
                ->name('users.updateRole');

            // Service Types
            Route::get('service-types',    [ServiceTypeController::class, 'index'])->name('service-types.index');
            Route::post('service-types',   [ServiceTypeController::class, 'store'])->name('service-types.store');
            Route::patch('service-types/{serviceType}', [ServiceTypeController::class, 'update'])->name('service-types.update');
            Route::delete('service-types/{serviceType}', [ServiceTypeController::class, 'destroy'])->name('service-types.destroy');

            // Service Catalog
            Route::get('service-catalog',                          [ServiceCatalogAdminController::class, 'index'])  ->name('service-catalog.index');
            Route::post('service-catalog',                         [ServiceCatalogAdminController::class, 'store'])  ->name('service-catalog.store');
            Route::put('service-catalog/{serviceCatalog}',         [ServiceCatalogAdminController::class, 'update']) ->name('service-catalog.update');
            Route::delete('service-catalog/{serviceCatalog}',      [ServiceCatalogAdminController::class, 'destroy'])->name('service-catalog.destroy');

            Route::get('roles',
                [AdminController::class, 'roles'])
                ->name('roles.index');

            Route::post('roles',
                [AdminController::class, 'storeRole'])
                ->name('roles.store');

            Route::delete('roles/{role}',
                [AdminController::class, 'destroyRole'])
                ->name('roles.destroy');

            Route::patch('roles/{role}/permissions',
                [AdminController::class, 'updateRolePermissions'])
                ->name('roles.updatePermissions');

            // Audit Log
            Route::get('audit-log',
                [AdminController::class, 'auditLog'])
                ->name('audit-log.index');

            Route::get('audit-log/{service_request}',
                [AdminController::class, 'auditLogForRequest'])
                ->name('audit-log.show');
        });
});

require __DIR__.'/auth.php';
