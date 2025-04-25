<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ShopifyController;
use App\Http\Controllers\IdentifyProviderController;

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Registration Routes
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// OAuth Routes
Route::middleware('auth')->group(function () {
    Route::get('/home', function () {
        return view('home');
    })->name('home');
    
    // Shopify OAuth Routes
    Route::get('/shopify/connect', [ShopifyController::class, 'redirect'])->name('shopify.connect');
    Route::get('/shopify/callback', [ShopifyController::class, 'callback'])->name('shopify.callback');
    Route::post('/shopify/api', [ShopifyController::class, 'makeApiRequest'])->name('shopify.api');
});

// Identify Provider Routes
Route::get('/identify', [IdentifyProviderController::class, 'identify'])->name('identify.provider');
Route::post('/verify-token', [IdentifyProviderController::class, 'verifyToken'])->name('verify.token');

