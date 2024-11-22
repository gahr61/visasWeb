<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommissionsController;
use App\Http\Controllers\UsersController;

Route::group(['prefix'=>'v1'], function(){
    Route::group(['prefix'=>'auth'], function(){
    	Route::post('register', [AuthController::class, 'register']);
    	Route::post('verifyEmail', [AuthController::class, 'verifyEmail']);
        Route::post('login', [AuthController::class, 'login']);

        Route::group(['middleware'=>'auth:sanctum'], function(){
            Route::get('logout', [AuthController::class, 'logout']);
        });
    });

    Route::group(['middleware' => 'auth:sanctum'], function(){
        Route::controller(UsersController::class)->group(function(){
            Route::post('users/reset', 'restorePassword');
            Route::get('users/list', 'list');
        });

        Route::controller(CommissionsController::class)->group(function(){
            Route::get('commissions/list', 'list');
        });
    });
});