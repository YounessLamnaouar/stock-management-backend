<?php

use App\Http\Controllers\AlertStockController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// 2 may
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\EntrepotController;
use App\Http\Controllers\MovementStockController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\TracabiliteController;
use App\Http\Controllers\TypeMouvementController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// 3 may
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('categories', CategorieController::class);
    Route::apiResource('produits', ProduitController::class);
    Route::apiResource('entrepots', EntrepotController::class);
    Route::apiResource('stocks', StockController::class);
    Route::apiResource('type-mouvements', TypeMouvementController::class);
    Route::apiResource('movement-stocks', MovementStockController::class);
    Route::apiResource('alert-stocks', AlertStockController::class);
    Route::apiResource('tracabilites', TracabiliteController::class);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
