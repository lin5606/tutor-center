<?php

/**
 * TODO: Project scaffold for tutor-center; implementation guidance is recorded in inline TODO notes.
 */

use App\Http\Controllers\Admin\ClassManagementController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PaymentRefundController;
use App\Http\Controllers\Admin\TutorApprovalController;
use App\Http\Controllers\Admin\UserRoleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Parent\ParentClassController;
use App\Http\Controllers\Tutor\TutorClassController;
use App\Http\Controllers\Tutor\TutorProfileController;
use Illuminate\Support\Facades\Route;

// TODO: Public routes expose only published class fields; detail responses must never reveal private_address.
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/classes', [HomeController::class, 'classes'])->name('classes.index');
Route::get('/classes/{class}', [HomeController::class, 'showClass'])->name('classes.show');
Route::get('/tutors', [HomeController::class, 'tutors'])->name('tutors.index');

// TODO: Guests may register as parent/tutor or sign in; authenticated users are redirected from these forms.
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

// TODO: Shared authenticated routes include logout; feature groups enforce ownership in controllers.
Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // TODO: Parent endpoints create and manage owned class requests, schedules, and reviews.
    Route::prefix('parent')->name('parent.')->group(function (): void {
        Route::get('/classes', [ParentClassController::class, 'index'])->name('classes.index');
        Route::get('/classes/create', [ParentClassController::class, 'create'])->name('classes.create');
        Route::post('/classes', [ParentClassController::class, 'store'])->name('classes.store');
        Route::get('/classes/{class}/edit', [ParentClassController::class, 'edit'])->name('classes.edit');
        Route::put('/classes/{class}', [ParentClassController::class, 'update'])->name('classes.update');
        Route::delete('/classes/{class}', [ParentClassController::class, 'destroy'])->name('classes.destroy');
        Route::match(['get', 'post'], '/classes/{class}/review', [ParentClassController::class, 'review'])->name('classes.review');
    });

    // TODO: Tutor profile edits are available before approval; applying requires the approved-profile gate.
    Route::prefix('tutor')->name('tutor.')->group(function (): void {
        Route::get('/profile', [TutorProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [TutorProfileController::class, 'update'])->name('profile.update');
        Route::get('/classes', [TutorClassController::class, 'index'])->name('classes.index');
        Route::post('/classes/{class}/apply', [TutorClassController::class, 'apply'])
            ->middleware('tutor.approved')->name('classes.apply');
        Route::get('/applications', [TutorClassController::class, 'applications'])->name('applications.index');
        Route::get('/assigned-classes', [TutorClassController::class, 'assigned'])->name('classes.assigned');
        Route::post('/payments/{payment}/submit', [TutorClassController::class, 'submitPayment'])->name('payments.submit');
        Route::get('/refunds', [TutorClassController::class, 'refunds'])->name('refunds.index');
    });

    // TODO: Every admin route uses a granular permission slug, not a hard-coded staff role.
    Route::prefix('admin')->name('admin.')->group(function (): void {
        Route::get('/', [DashboardController::class, 'index'])
            ->middleware('permission:dashboard.view')->name('dashboard');
        Route::get('/users-roles', [UserRoleController::class, 'index'])
            ->middleware('permission:users.manage')->name('users_roles.index');
        Route::post('/users/{user}/toggle', [UserRoleController::class, 'toggleUser'])
            ->middleware('permission:users.manage')->name('users.toggle');
        Route::put('/users/{user}/roles', [UserRoleController::class, 'assignRoles'])
            ->middleware('permission:roles.manage')->name('users.roles');
        Route::post('/roles', [UserRoleController::class, 'storeRole'])
            ->middleware('permission:roles.manage')->name('roles.store');
        Route::put('/roles/{role}', [UserRoleController::class, 'updateRole'])
            ->middleware('permission:roles.manage')->name('roles.update');
        Route::delete('/roles/{role}', [UserRoleController::class, 'destroyRole'])
            ->middleware('permission:roles.manage')->name('roles.destroy');

        Route::get('/tutors', [TutorApprovalController::class, 'index'])
            ->middleware('permission:tutors.approve')->name('tutors.index');
        Route::post('/tutors/{profile}/approve', [TutorApprovalController::class, 'approve'])
            ->middleware('permission:tutors.approve')->name('tutors.approve');
        Route::post('/tutors/{profile}/reject', [TutorApprovalController::class, 'reject'])
            ->middleware('permission:tutors.approve')->name('tutors.reject');

        Route::get('/classes', [ClassManagementController::class, 'index'])
            ->middleware('permission:classes.manage')->name('classes.index');
        Route::post('/classes/{class}/approve', [ClassManagementController::class, 'approve'])
            ->middleware('permission:classes.manage')->name('classes.approve');
        Route::post('/classes/{class}/reject', [ClassManagementController::class, 'reject'])
            ->middleware('permission:classes.manage')->name('classes.reject');
        Route::post('/classes/{class}/applications/{application}/assign', [ClassManagementController::class, 'assign'])
            ->middleware('permission:classes.manage')->name('classes.assign');

        Route::get('/payments-refunds', [PaymentRefundController::class, 'index'])
            ->middleware('permission:payments.manage')->name('payments_refunds.index');
        Route::post('/payments/{payment}/confirm', [PaymentRefundController::class, 'confirm'])
            ->middleware('permission:payments.manage')->name('payments.confirm');
        Route::post('/payments/{payment}/reject', [PaymentRefundController::class, 'reject'])
            ->middleware('permission:payments.manage')->name('payments.reject');
        Route::post('/payments/{payment}/refunds', [PaymentRefundController::class, 'createRefund'])
            ->middleware('permission:refunds.manage')->name('refunds.store');
        Route::post('/refunds/{refund}/process', [PaymentRefundController::class, 'processRefund'])
            ->middleware('permission:refunds.manage')->name('refunds.process');
    });
});
