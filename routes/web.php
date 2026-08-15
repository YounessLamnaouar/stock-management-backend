<?php

use Illuminate\Support\Facades\Route;

// Global preflight OPTIONS handler
Route::options('{any}', function () {
    $origin = request()->header('Origin') ?: '*';
    return response('', 200)
        ->header('Access-Control-Allow-Origin', $origin)
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin')
        ->header('Access-Control-Allow-Credentials', 'true');
})->where('any', '.*');

Route::get('/', function () {
    return view('welcome');
});
