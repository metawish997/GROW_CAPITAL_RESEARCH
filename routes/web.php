<?php

use App\Http\Controllers\Api\KycController;
use Illuminate\Support\Facades\Route;

// Redirect root to user login
Route::get('/', fn() => redirect('/login'));

// --- User Auth Views ---
Route::get('/login',     fn() => view('auth.login'))->name('login');
Route::get('/dashboard', fn() => view('user.dashboard'))->name('dashboard');
Route::get('/kyc',       fn() => redirect('/dashboard'))->name('kyc');

// --- KYC Callback (Digio redirects here after user completes KYC) ---
Route::get('/kyc/callback', [KycController::class, 'callback'])->name('kyc.callback');

// --- Guest KYC Media Upload Links ---
Route::get('/kyc/upload-media/{user_id}/{token}', [\App\Http\Controllers\KycMediaUploadController::class, 'showForm'])->name('kyc.upload_media.form');
Route::post('/kyc/upload-media/{user_id}/{token}', [\App\Http\Controllers\KycMediaUploadController::class, 'submitForm'])->name('kyc.upload_media.submit');

// --- Admin Views ---
Route::get('/admin', fn() => redirect('/admin/login'));
Route::get('/admin/login',     fn() => view('admin.login'))->name('admin.login');
Route::get('/admin/dashboard', fn() => view('admin.dashboard'))->name('admin.dashboard');
Route::get('/admin/settings',  fn() => view('admin.settings'))->name('admin.settings');
Route::get('/admin/users',     fn() => view('admin.users.index'))->name('admin.users.index');
Route::get('/admin/users/{id}', fn($id) => view('admin.users.show', ['id' => $id]))->name('admin.users.show');
Route::get('/admin/team',       fn() => view('admin.team.index'))->name('admin.team.index');
Route::get('/admin/change-password', fn() => view('admin.change-password'))->name('admin.change-password');
Route::get('/admin/reset-password',  fn() => view('admin.reset-password'))->name('admin.reset-password');
 
// --- Admin KRA Settings ---
Route::post('/admin/users/{id}/reupload', [\App\Http\Controllers\Admin\KraSettingsController::class, 'reupload'])->name('admin.users.reupload');

