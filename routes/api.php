<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommissionsController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\RolesController;
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

        Route::controller(PermissionsController::class)->group(function(){
            Route::get('permissions', 'fullList');
            Route::get('permissions/assigned/{id}', 'permissionsAssigned');
            Route::put('permissions/assign', 'assign');
            Route::put('permissions/design', 'design');
        });

        Route::controller(RolesController::class)->group(function(){
            Route::get('roles/{id}', 'edit');
            Route::delete('roles/{id}', 'delete');
            Route::get('roles', 'fullList');
            Route::post('roles', 'store');
            Route::put('roles/{id}', 'update');

        });
    });
});