<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/users', function (Request $request) {
    // Validação simples
    $dados = $request->validate([
        'nome' => 'required|string|max:255',
        'local' => 'required|string',
        'ano' => 'required|integer',
        'data_inicio' => 'required|date',
        'duracao' => 'required|integer',
        'tipo' => 'required|string',
    ]);

    // Salva no banco usando o Model Event
    App\Models\Event::create($dados);

    // Recarrega a página para mostrar o novo evento
    return back();
});
