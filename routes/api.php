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
    Route::post('/forgot-password', [AdminAuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AdminAuthController::class, 'resetPassword']);
});

// --------------------------
// Protected: Admin Routes
// --------------------------
Route::prefix('admin')->middleware(['auth:sanctum', AdminMiddleware::class])->group(function () {
    Route::get('/me',     [AdminAuthController::class, 'me']);
    Route::post('/logout', [AdminAuthController::class, 'logout']);
    Route::post('/change-password', [AdminAuthController::class, 'changePassword']);

    // API Credentials / Settings
    Route::prefix('settings')->group(function () {
        Route::get('/',             [AppSettingController::class, 'index']);
        Route::post('/kra/test-soap', [\App\Http\Controllers\Admin\KraSettingsController::class, 'testSoap']);
        Route::post('/kra/test-upload', [\App\Http\Controllers\Admin\KraSettingsController::class, 'testUpload']);
        Route::get('/{group}',      [AppSettingController::class, 'show']);
        Route::post('/{group}',     [AppSettingController::class, 'update']);
    });

    // User Management (Customers List)
    Route::prefix('users')->group(function () {
        Route::get('/',             [AdminUserController::class, 'index']);
        Route::post('/',            [AdminUserController::class, 'store']);
        Route::get('/dashboard-stats', [AdminUserController::class, 'dashboardStats']);
        Route::get('/{id}',         [AdminUserController::class, 'show']);
        Route::put('/{id}',         [AdminUserController::class, 'update']);
        Route::get('/{id}/esign',   [AdminUserController::class, 'downloadEsign']);
        Route::post('/{id}/resend-agreement-email', [AdminUserController::class, 'resendAgreementEmail']);
        Route::get('/{id}/download-consolidated', [AdminUserController::class, 'downloadConsolidated']);
        Route::post('/{id}/generate-digio-kyc-link', [AdminUserController::class, 'generateDigioKycLink']);
        Route::get('/{id}/media/{type}', [AdminUserController::class, 'getKycMedia']);
        Route::post('/{id}/media/{type}', [AdminUserController::class, 'uploadKycMedia']);
    });

    // Team Management (Admin/Staff List)
    Route::prefix('team')->group(function () {
        Route::get('/',             [\App\Http\Controllers\Api\AdminTeamController::class, 'index']);
        Route::post('/',            [\App\Http\Controllers\Api\AdminTeamController::class, 'store']);
        Route::put('/{id}',         [\App\Http\Controllers\Api\AdminTeamController::class, 'update']);
        Route::delete('/{id}',      [\App\Http\Controllers\Api\AdminTeamController::class, 'destroy']);
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
