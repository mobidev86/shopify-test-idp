<?php

use Illuminate\Support\Facades\Route;
use CodeGreenCreative\SamlIdp\Http\Controllers\MetadataController;
use CodeGreenCreative\SamlIdp\Http\Controllers\LogoutController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/saml/metadata', [MetadataController::class, 'index']);
Route::match(['get', 'post'], '/saml/sso', [MetadataController::class, 'index']);
Route::match(['get', 'post'], '/saml/slo', [LogoutController::class, 'index']);

