<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EntrepotController;
use App\Http\Controllers\MovementStockController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\StatusMouvementController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\TracabiliteController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Preflight OPTIONS handler
Route::options('/{any}', function () {
    return response('', 204);
})->where('any', '.*');

// Auth (public)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/me',               [AuthController::class, 'me']);
    Route::put('/profile',          [AuthController::class, 'updateProfile']);
    Route::put('/profile/password', [AuthController::class, 'updatePassword']);
    Route::post('/logout',          [AuthController::class, 'logout']);

    // Dashboard stats — accessible by all roles
    Route::get('/dashboard', [DashboardController::class, 'stats']);

    // ── Agent + Gestionnaire + Admin ──
    Route::get('categories',              [CategorieController::class, 'index']);
    Route::get('categories/{categorie}',  [CategorieController::class, 'show']);

    Route::get('produits',                [ProduitController::class, 'index']);
    Route::get('produits/{produit}',      [ProduitController::class, 'show']);

    Route::get('entrepots',               [EntrepotController::class, 'index']);
    Route::get('entrepots/{entrepot}',    [EntrepotController::class, 'show']);

    Route::get('stocks',                  [StockController::class, 'index']);
    Route::get('stocks/{stock}',          [StockController::class, 'show']);

    Route::get('movement-stocks',                    [MovementStockController::class, 'index']);
    Route::get('movement-stocks/{movementStock}',    [MovementStockController::class, 'show']);
    Route::post('movement-stocks',                   [MovementStockController::class, 'store']);
    Route::get('movement-stocks/export/csv',         [MovementStockController::class, 'export']);

    Route::get('status-mouvements', [StatusMouvementController::class, 'index']);

    // ── Gestionnaire + Admin ──
    Route::middleware('role:Gestionnaire,Admin')->group(function () {

        Route::post('stocks',            [StockController::class, 'store']);
        Route::put('stocks/{stock}',     [StockController::class, 'update']);
        Route::patch('stocks/{stock}',   [StockController::class, 'update']);
        Route::delete('stocks/{stock}',  [StockController::class, 'destroy']);

        Route::put('movement-stocks/{movementStock}',    [MovementStockController::class, 'update']);
        Route::patch('movement-stocks/{movementStock}',  [MovementStockController::class, 'update']);
        Route::delete('movement-stocks/{movementStock}', [MovementStockController::class, 'destroy']);

        Route::get('tracabilites',               [TracabiliteController::class, 'index']);
        Route::get('tracabilites/{tracabilite}', [TracabiliteController::class, 'show']);
    });

    // ── Admin only ──
    Route::middleware('role:Admin')->group(function () {

        Route::post('categories',              [CategorieController::class, 'store']);
        Route::put('categories/{categorie}',   [CategorieController::class, 'update']);
        Route::patch('categories/{categorie}', [CategorieController::class, 'update']);
        Route::delete('categories/{categorie}',[CategorieController::class, 'destroy']);

        Route::post('produits',              [ProduitController::class, 'store']);
        Route::put('produits/{produit}',     [ProduitController::class, 'update']);
        Route::patch('produits/{produit}',   [ProduitController::class, 'update']);
        Route::delete('produits/{produit}',  [ProduitController::class, 'destroy']);

        Route::post('entrepots',             [EntrepotController::class, 'store']);
        Route::put('entrepots/{entrepot}',   [EntrepotController::class, 'update']);
        Route::patch('entrepots/{entrepot}', [EntrepotController::class, 'update']);
        Route::delete('entrepots/{entrepot}',[EntrepotController::class, 'destroy']);

        Route::apiResource('users', UserController::class);

        Route::post('status-mouvements',                    [StatusMouvementController::class, 'store']);
        Route::put('status-mouvements/{statusMouvement}',   [StatusMouvementController::class, 'update']);
        Route::patch('status-mouvements/{statusMouvement}', [StatusMouvementController::class, 'update']);
        Route::delete('status-mouvements/{statusMouvement}',[StatusMouvementController::class, 'destroy']);
    });
});
