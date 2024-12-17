<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SupplierController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Categories Routes
    Route::apiResource('categories', CategoryController::class);
    
    // Products Routes
    Route::apiResource('products', ProductController::class);
    
    // Suppliers Routes
    Route::apiResource('suppliers', SupplierController::class);

    // routes/api.php
    Route::post('v1/categories/bulk', [CategoryController::class, 'bulkStore']);
});