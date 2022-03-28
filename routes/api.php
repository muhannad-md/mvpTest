<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function(){

    // Route::apiResource('/users', UserController::class, ['only' => ['store']]);
    
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    Route::middleware('auth:api')->group(function(){
    
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout/all', [AuthController::class, 'logoutAll']);

        Route::apiResource('/products', ProductController::class);
        Route::apiResource('/users', UserController::class, ['except' => ['store']]);
        
        Route::post('/deposit', [UserController::class, 'deposit']);
        Route::post('/reset', [UserController::class, 'reset']);

        Route::post('/buy', [ProductController::class, 'buy']);
        
    });
    
});