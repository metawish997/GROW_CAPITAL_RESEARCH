<?php

use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\AppSettingController;
use App\Http\Controllers\Api\KycController;
use App\Http\Controllers\Api\UserAuthController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Grow Capitals Research
|--------------------------------------------------------------------------
| All routes here use Laravel Sanctum for authentication.
| All responses are JSON - suitable for Web & Mobile apps.
*/

// --------------------------
// Public: User Auth (OTP)
// --------------------------
Route::prefix('user')->group(function () {
    Route::post('/send-otp',   [UserAuthController::class, 'sendOtp']);
    Route::post('/verify-otp', [UserAuthController::class, 'verifyOtp']);
});

// --------------------------
// Protected: User Routes
// --------------------------
Route::prefix('user')->middleware('auth:sanctum')->group(function () {
    Route::get('/me',     [UserAuthController::class, 'me']);
    Route::post('/logout', [UserAuthController::class, 'logout']);

    // KYC
    Route::get('/kyc/status',    [KycController::class, 'status']);
    Route::post('/kyc/initiate', [KycController::class, 'initiate']);
    Route::post('/kyc/sync',     [KycController::class, 'sync']);

    // E-Sign
    Route::get('/esign/status',   [\App\Http\Controllers\Api\EsignAgreementController::class, 'status']);
    Route::get('/esign/preview',  [\App\Http\Controllers\Api\EsignAgreementController::class, 'preview']);
    Route::post('/esign/sign',    [\App\Http\Controllers\Api\EsignAgreementController::class, 'sign']);
    Route::get('/esign/download', [\App\Http\Controllers\Api\EsignAgreementController::class, 'download']);
});

// --------------------------
// Public: Admin Auth (Password)
// --------------------------
Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login']);
});

// --------------------------
// Protected: Admin Routes
// --------------------------
Route::prefix('admin')->middleware(['auth:sanctum', AdminMiddleware::class])->group(function () {
    Route::get('/me',     [AdminAuthController::class, 'me']);
    Route::post('/logout', [AdminAuthController::class, 'logout']);

    // API Credentials / Settings
    Route::prefix('settings')->group(function () {
        Route::get('/',             [AppSettingController::class, 'index']);
        Route::get('/{group}',      [AppSettingController::class, 'show']);
        Route::post('/{group}',     [AppSettingController::class, 'update']);
    });

    // User Management
    Route::prefix('users')->group(function () {
        Route::get('/',             [AdminUserController::class, 'index']);
        Route::get('/{id}',         [AdminUserController::class, 'show']);
        Route::get('/{id}/esign',   [AdminUserController::class, 'downloadEsign']);
    });

    // KYC Management
    Route::prefix('kyc')->group(function () {
        Route::get('/',            [KycController::class, 'adminIndex']);   // List all KYC
        Route::get('/{id}',        [KycController::class, 'adminShow']);    // Single KYC detail
        Route::post('/{id}/approve', [KycController::class, 'adminApprove']); // Approve
        Route::post('/{id}/reject',  [KycController::class, 'adminReject']); // Reject
        Route::post('/{id}/sync',    [KycController::class, 'adminSync']);  // Force sync from Digio
    });
});
