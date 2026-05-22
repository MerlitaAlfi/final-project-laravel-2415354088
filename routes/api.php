<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ServiceController; // Ini tambahan kita

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Ini tambahan Route untuk fitur Service kita
Route::apiResource("services", ServiceController::class);
Route::patch("services/{service}/activate", [ServiceController::class, "activate"]);
Route::patch("services/{service}/deactivate", [ServiceController::class, "deactivate"]);