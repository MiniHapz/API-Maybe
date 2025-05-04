<?php

use Illuminate\Auth\Middleware\Authenticate;

use Illuminate\Http\Request;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//post untuk post
Route::apiResource('/posts', App\Http\Controllers\Api\PostController::class);

//post untuk kategori
Route::apiResource('/kategori', App\Http\Controllers\Api\KategoriController::class);

//post untuk savings
Route::apiResource('/saving', App\Http\Controllers\Api\SavingsController::class);

//post untuk budgets
Route::apiResource('/budgets', App\Http\Controllers\Api\BudgetsController::class);