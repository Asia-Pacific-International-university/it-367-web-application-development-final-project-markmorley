<?php

use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\MonthlyDuesController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ReportController;
use Illuminate\Support\Facades\Route;

/*
| All routes here are prefixed with /api and return JSON.
| Run `php artisan route:list --path=api` to see them all.
*/

// Public, rate limited: at most 6 attempts per minute per IP (a security control)
Route::middleware('throttle:6,1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Requires "Authorization: Bearer <token>"
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Announcement resource.
    Route::apiResource('announcements', AnnouncementController::class);
    Route::apiResource('payments', PaymentController::class)->only(['index', 'show', 'store']);
    Route::get('my/payments', [PaymentController::class, 'mine']);
    Route::apiResource('monthly-dues', MonthlyDuesController::class)
        ->parameters(['monthly-dues' => 'monthly_due']);
    Route::get('my/monthly-dues', [MonthlyDuesController::class, 'mine']);
    Route::apiResource('members', MemberController::class);
    Route::get('members/{member}/payments', [MemberController::class, 'payments']);
    Route::get('reports/monthly', [ReportController::class, 'monthly']);
    Route::get('reports/annual', [ReportController::class, 'annual']);
});
