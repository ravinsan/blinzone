<?php

use App\Http\Controllers\Api\Admin\VendorBankDetailController;
use App\Http\Controllers\Api\Admin\VendorAddressController;
use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\CountryController;
use App\Http\Controllers\Api\Admin\VendorController;
use App\Http\Controllers\Api\Admin\SliderController;
use App\Http\Controllers\Api\Admin\StateController;
use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\CityController;
use App\Http\Controllers\Api\Admin\DeliveryZoneController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {

    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('api.auth')->group(function () {
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::post('/logout', [AuthController::class, 'logout']);

        //Category
        Route::prefix('categories')->group(function () 
        { 
            Route::get('/', [CategoryController::class, 'index']); 
            Route::post('/', [CategoryController::class, 'store']); 
            Route::get('/{id}', [CategoryController::class, 'show']); 
            Route::post('/{id}', [CategoryController::class, 'update']); 
            Route::delete('/{id}', [CategoryController::class, 'destroy']); 
            Route::get('/{id}/status', [CategoryController::class, 'changeStatus']); 
        });

        //Sliders
        Route::prefix('sliders')->group(function () 
        { 
            Route::get('/', [SliderController::class, 'index']); 
            Route::post('/', [SliderController::class, 'store']); 
            Route::get('/{id}', [SliderController::class, 'show']); 
            Route::post('/{id}', [SliderController::class, 'update']); 
            Route::delete('/{id}', [SliderController::class, 'destroy']); 
            Route::get('/{id}/status', [SliderController::class, 'changeStatus']); 
        });

        // Vendor
        Route::prefix('vendors')->group(function () {
            Route::get('/', [VendorController::class, 'index']);
            Route::post('/', [VendorController::class, 'store']);
            Route::get('/{id}', [VendorController::class, 'show']);
            Route::post('/{id}', [VendorController::class, 'update']);
            Route::delete('/{id}', [VendorController::class, 'destroy']);
            Route::get('/{id}/status', [VendorController::class, 'changeStatus']);
        });

        // Vendor Address
        Route::prefix('vendor-addresses')->group(function () {
            Route::get('/{vendor_id}', [VendorAddressController::class, 'index']);
            Route::post('/{vendor_id}', [VendorAddressController::class, 'store']);
            Route::get('/{id}/{vendor_id}', [VendorAddressController::class, 'show']);
            Route::post('/{id}/{vendor_id}', [VendorAddressController::class, 'update']);
            Route::delete('/{id}/{vendor_id}', [VendorAddressController::class, 'destroy']);
        });

        // Vendor Bank Detail
        Route::prefix('vendor-bank-details')->group(function () {
            Route::get('/', [VendorBankDetailController::class, 'index']);
            Route::post('/', [VendorBankDetailController::class, 'store']);
            Route::get('/{id}', [VendorBankDetailController::class, 'show']);
            Route::post('/{id}', [VendorBankDetailController::class, 'update']);
            Route::delete('/{id}', [VendorBankDetailController::class, 'destroy']);
        });

        // Country
        Route::prefix('countries')->group(function () {
            Route::get('/', [CountryController::class, 'index']);
            Route::post('/', [CountryController::class, 'store']);
            Route::get('/{id}', [CountryController::class, 'show']);
            Route::post('/{id}', [CountryController::class, 'update']);
            Route::delete('/{id}', [CountryController::class, 'destroy']);
        });

        // State
        Route::prefix('states')->group(function () {
            Route::get('/{country_id}', [StateController::class, 'index']);
            Route::post('/{country_id}', [StateController::class, 'store']);
            Route::get('/{id}/{country_id}', [StateController::class, 'show']);
            Route::post('/{id}/{country_id}', [StateController::class, 'update']);
            Route::delete('/{id}/{country_id}', [StateController::class, 'destroy']);
        });

        // City
        Route::prefix('cities')->group(function () {
            Route::get('/{state_id}', [CityController::class, 'index']);
            Route::post('/{state_id}', [CityController::class, 'store']);
            Route::get('/{id}/{state_id}', [CityController::class, 'show']);
            Route::post('/{id}/{state_id}', [CityController::class, 'update']);
            Route::delete('/{id}/{state_id}', [CityController::class, 'destroy']);
        });

        // Delivery Zone
        Route::prefix('delivery-zones')->group(function () {
            Route::get('/', [DeliveryZoneController::class, 'index']);
            Route::post('/', [DeliveryZoneController::class, 'store']);
            Route::get('/{id}', [DeliveryZoneController::class, 'show']);
            Route::post('/{id}', [DeliveryZoneController::class, 'update']);
            Route::delete('/{id}', [DeliveryZoneController::class, 'destroy']);
        });
    });

});