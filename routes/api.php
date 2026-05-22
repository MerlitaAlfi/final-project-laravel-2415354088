<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\SubscriptionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route untuk fitur Service
Route::apiResource("services", ServiceController::class);
Route::patch("services/{service}/activate", [ServiceController::class, "activate"]);
Route::patch("services/{service}/deactivate", [ServiceController::class, "deactivate"]);

// Route untuk fitur Customer
Route::apiResource("customers", CustomerController::class);
Route::patch("customers/{customer}/activate", [CustomerController::class, "activate"]);
Route::patch("customers/{customer}/deactivate", [CustomerController::class, "deactivate"]);

// Route untuk fitur Subscription (Khusus ini HANYA index, store, dan show. Tanpa update & destroy)
Route::apiResource("subscriptions", SubscriptionController::class)->only(['index', 'store', 'show']);
Route::patch("subscriptions/{subscription}/activate", [SubscriptionController::class, "activate"]);
Route::patch("subscriptions/{subscription}/deactivate", [SubscriptionController::class, "deactivate"]);