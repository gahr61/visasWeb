<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchOfficeController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\CountriesController;
use App\Http\Controllers\SalesConceptsController;
use App\Http\Controllers\SalesBillingController;
use App\Http\Controllers\CommissionsController;
use App\Http\Controllers\PassportController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\ProceduresController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SalesTokenController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\OccupationsController;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\ScheduleController;

Route::group(['prefix'=>'v1'], function(){
    Route::group(['prefix'=>'auth'], function(){
    	Route::post('register', [AuthController::class, 'register']);
    	Route::post('verifyEmail', [AuthController::class, 'verifyEmail']);
        Route::post('login', [AuthController::class, 'login']);

        Route::group(['middleware'=>'auth:sanctum'], function(){
            Route::get('logout', [AuthController::class, 'logout']);
        });
    });

    //verify token
    Route::controller(SalesTokenController::class)->group(function(){
        Route::post('sales/validate/token', 'verifyToken');
    });

    Route::post('sales/clients/confirm', [SalesBillingController::class, 'visaPaymentUpdate']);
    
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

        Route::controller(ClientsController::class)->group(function(){
            Route::post('clients/confirm/payment', 'clientsConfirmVisaPayment');
            Route::put('clients/update/{id}', 'clientsUpdate');

            Route::put('clients/address/update', 'clientsUpdateAddress');
            Route::put('clients/phones/update', 'clientsUpdatePhones');
            Route::post('clients/parents', 'clientsRelationships');
            Route::post('clients/occupations', 'clientsSaveUpdateOccupation');
            Route::post('clients/studies', 'clientsSaveSchools');
            Route::post('clients/travel', 'clientsSaveTravel');
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

        Route::controller(OccupationsController::class)->group(function(){
            Route::get('occupations/list', 'list');
            Route::get('occupations/{id}/edit', 'edit');
            Route::post('occupations', 'store');
            Route::put('occupations/{id}/update', 'update');
            Route::delete('occupations/{id}/delete', 'destroy');
        });

        Route::controller(PassportController::class)->group(function(){
            Route::put('passport/{id}/update', 'update');
        });

        Route::controller(PermissionsController::class)->group(function(){
            Route::get('permissions', 'fullList');
            Route::get('permissions/assigned/{id}', 'permissionsAssigned');
            Route::put('permissions/assign', 'assign');
            Route::put('permissions/design', 'design');
        });

        Route::controller(ProceduresController::class)->group(function(){
            Route::get('procedures/visas/{id}/details', 'infoVisasDetails');
        });

        Route::controller(ProcessController::class)->group(function(){
            Route::put('process/ds_160/update', 'updateDS160');
            Route::put('process/account/update', 'processAccountUpdate');
        });

        Route::controller(SalesController::class)->group(function(){
            Route::post('sales/visa', 'visa_store');
            Route::get('sales/visa/list', 'visa_list');
            Route::get('sales/{id}/details', 'info');
        });

        Route::controller(SalesBillingController::class)->group(function(){
            Route::post('sales/visa/payment', 'sendVisaPayment');
            Route::post('sales/visa/payment/confirm', 'visaPaymentUpdate');
            Route::get('sales/visa/payment/list/{id}', 'visaPaymentList');
        });

        Route::controller(SalesConceptsController::class)->group(function(){
            Route::post('concepts', 'store');
            Route::get('concepts/list', 'index');
            Route::get('concepts/{id}/edit', 'show');
            Route::get('concepts/{id}/history', 'history');
            Route::put('concepts/{id}/update', 'update');
            Route::get('concepts/visa', 'visaPrices');
        });

        Route::controller(ScheduleController::class)->group(function(){
            Route::put('schedule/details', 'update');
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
            Route::post('users/confirm/payment', 'userConfirmVisaPayment');
        });
    });
});