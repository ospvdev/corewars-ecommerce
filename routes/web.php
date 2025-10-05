<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\SteamAuthController;
use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\UserController;

Route::get('/', [UserController::class, 'index'])->name('home');
Route::get('/test', [UserController::class, 'test'])->name('test');

Route::prefix('auth/steam')->group(function () {
    Route::get('/', SteamAuthController::class)->name('steam.auth');
    Route::get('/login', [SteamAuthController::class, 'login'])->name('login');
    Route::post('/logout', [SteamAuthController::class, 'logout'])->name('steam.logout')->middleware('auth');
});

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', [DashboardAdminController::class, 'index'])->name('dashboard');
});