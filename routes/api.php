<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProductoController;
use App\Http\Controllers\API\EmpleadoController;
// librerias
use App\Http\Controllers\Api\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//Route::apiResource('productos', ProductoController::class)->names('api.productos');
//Route::apiResource('empleados', EmpleadoController::class)->names('api.empleados');

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function() {
        Route::apiResource('productos', ProductoController::class)->names('api.productos');
        Route::apiResource('empleados', EmpleadoController::class)->names('api.empleados');

        });