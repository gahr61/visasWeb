<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchOfficeController;
use App\Http\Controllers\CountriesController;
use App\Http\Controllers\SalesConceptsController;
use App\Http\Controllers\CommissionsController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\SalesController;
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
        Route::controller(BranchOfficeController::class)->group(function(){
            Route::delete('branchOffice/{id}/delete', 'delete');
            Route::get('branchOffice/{id}/edit', 'edit');
            Route::get('branchOffice/list', 'list');
            Route::post('branchOffice', 'store');
            Route::put('branchOffice/{id}/update', 'update');
        });        

        Route::controller(CountriesController::class)->group(function(){
            Route::get('countries/list', 'countries');
            Route::get('countries/states/{id}', 'statesByCountry');
        });

        Route::controller(SalesConceptsController::class)->group(function(){
            Route::post('concepts', 'store');
            Route::get('concepts/list', 'index');
            Route::get('concepts/{id}/edit', 'show');
            Route::get('concepts/{id}/history', 'history');
            Route::put('concepts/{id}/update', 'update');
            Route::get('concepts/visa', 'visaPrices');
        });

        Route::controller(CommissionsController::class)->group(function(){
            Route::delete('commissions/{id}/delete', 'delete');
            Route::get('commissions/{id}/edit', 'edit');
            Route::get('commissions/list', 'list');
            Route::post('commissions', 'store');
            Route::put('commissions/update', 'update');
            Route::put('commissions/users/update', 'userUpdate');
            Route::get('commissions/{id}/verify/users', 'verifyCommissionUser');
        });

        Route::controller(PermissionsController::class)->group(function(){
            Route::get('permissions', 'fullList');
            Route::get('permissions/assigned/{id}', 'permissionsAssigned');
            Route::put('permissions/assign', 'assign');
            Route::put('permissions/design', 'design');
        });

        Route::controller(SalesController::class)->group(function(){
            Route::post('sales/visa', 'visa_store');
            Route::get('sales/visa/list', 'visa_list');
        });

        Route::controller(RolesController::class)->group(function(){
            Route::get('roles/{id}/edit', 'edit');
            Route::delete('roles/{id}/delete', 'delete');
            Route::get('roles', 'fullList');
            Route::get('roles/list', 'list');
            Route::post('roles', 'store');
            Route::put('roles/{id}/update', 'update');
        });

        Route::controller(UsersController::class)->group(function(){
            Route::get('users/{id}/edit', 'edit');
            Route::delete('users/{id}/delete', 'delete');
            Route::get('users/list', 'list');
            Route::post('users/reset', 'restorePassword');
            Route::get('users/sales', 'sales');
            Route::post('users', 'store');
            Route::put('users/{id}/update', 'update');
        });
    });
});