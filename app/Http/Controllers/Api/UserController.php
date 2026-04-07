<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cpf'               => 'required|string|size:11|unique:users',
            'nome'              => 'required|string|max:255',
            'email'             => 'required|email|unique:users',
            'password'          => 'required|min:8',
            'dt_nasc'           => 'required|date',
            'sexo'              => 'required|in:M,F',
            'role_id'           => 'required|exists:roles,id',
            'marital_status_id' => 'required|exists:marital_statuses,id',
        ]);

        $validated['password'] = bcrypt($validated['password']); // Criptografia!

        $user = User::create($validated);

        return response()->json([
            'message' => 'Usuário criado com sucesso!',
            'user' => $user
        ], 201);
    }

    public function show($id)
    {
        $user = User::with(['role', 'maritalStatus'])->find($id);
        return $user ? response()->json($user) : response()->json(['m' => 'Erro'], 404);
    }
}
