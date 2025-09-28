<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\SteamAuthController;
use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\UserController;

Route::get('/', [UserController::class, 'index'])->name('home');
Route::get('/test', [UserController::class, 'test'])->name('test');

Route::get('/auth/steam', SteamAuthController::class)->name('steam.auth');
Route::get('/login', [SteamAuthController::class, 'login'])->name('login');
Route::get('/auth/steam/logout', [SteamAuthController::class, 'logout'])->name('steam.logout')->middleware('auth');

Route::get('/admin/dashboard',[DashboardAdminController::class, 'index'])->name('admin.dashboard')->middleware('auth');
