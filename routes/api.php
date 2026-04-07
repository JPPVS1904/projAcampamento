<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\MaritalStatusController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;

// Rota pública para cadastro de novos usuários
Route::post('/register', [UserController::class, 'store']);

// Rota de Login (exemplo simplificado)
Route::post('/login', function (Request $request) {
    $user = App\Models\User::where('email', $request->email)->first();
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Credenciais inválidas'], 401);
    }
    return response()->json(['token' => $user->createToken('svelte-token')->plainTextToken]);
});

// Rotas Protegidas (Só acessa quem tem o Token)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', function (Request $request) {
        return $request->user(); // Retorna os dados do usuário logado
    });
    Route::apiResource('users', UserController::class)->except(['store']);
});

// Criará automaticamente as rotas GET, POST, PUT e DELETE para os métodos do Controller
Route::apiResource('events', EventController::class);
Route::apiResource('roles', RoleController::class);
Route::apiResource('marital-statuses', MaritalStatusController::class);

