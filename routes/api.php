<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PreRegistrationController;
use Illuminate\Support\Facades\Route;

// Rotas públicas
Route::get('/events', [EventController::class, 'index']);

// Rotas protegidas (Usuário precisa estar logado no Svelte para acessar)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/events', [EventController::class, 'store']);
    Route::get('/pre-registrations', [PreRegistrationController::class, 'index']);
});
