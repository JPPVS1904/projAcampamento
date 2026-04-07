<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\MaritalStatusController;

// Criará automaticamente as rotas GET, POST, PUT e DELETE para os métodos do Controller
Route::apiResource('events', EventController::class);
Route::apiResource('roles', RoleController::class);
Route::apiResource('marital-statuses', MaritalStatusController::class);

