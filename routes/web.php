<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\SteamAuthController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('/auth/steam', SteamAuthController::class)->name('steam.auth');