<?php

use App\Http\Controllers\Api\KycController;
use Illuminate\Support\Facades\Route;

// Redirect root to user login
Route::get('/', fn() => redirect('/login'));

// --- User Auth Views ---
Route::get('/login',     fn() => view('auth.login'))->name('login');
Route::get('/dashboard', fn() => view('user.dashboard'))->name('dashboard');
Route::get('/kyc',       fn() => view('user.kyc'))->name('kyc');

// --- KYC Callback (Digio redirects here after user completes KYC) ---
Route::get('/kyc/callback', [KycController::class, 'callback'])->name('kyc.callback');

// --- Admin Views ---
Route::get('/admin/login',     fn() => view('admin.login'))->name('admin.login');
Route::get('/admin/dashboard', fn() => view('admin.dashboard'))->name('admin.dashboard');
Route::get('/admin/settings',  fn() => view('admin.settings'))->name('admin.settings');
Route::get('/admin/users',     fn() => view('admin.users.index'))->name('admin.users.index');
Route::get('/admin/users/{id}', fn($id) => view('admin.users.show', ['id' => $id]))->name('admin.users.show');
