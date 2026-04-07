<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // Listar todos os eventos: GET /api/events
    public function index()
    {
        return response()->json(Event::all(), 200);
    }

    // Criar um evento: POST /api/events
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome'        => 'required|string|max:255',
            'local'       => 'required|string',
            'ano'         => 'required|integer',
            'data_inicio' => 'required|date',
            'duracao'     => 'required|integer',
            'tipo'        => 'required|string',
            'img'         => 'nullable|string'
        ]);

        $event = Event::create($validated);
        return response()->json($event, 201); // 201 = Criado com sucesso
    }

    // Mostrar um evento específico: GET /api/events/{id}
    public function show($id)
    {
        $event = Event::find($id);
        if (!$event) return response()->json(['message' => 'Evento não encontrado'], 404);
        return response()->json($event, 200);
    }

    // Deletar (SoftDelete): DELETE /api/events/{id}
    public function destroy($id)
    {
        $event = Event::find($id);
        if (!$event) return response()->json(['message' => 'Erro ao excluir'], 404);

        $event->delete();
        return response()->json(['message' => 'Evento excluído com sucesso'], 200);
    }
}
