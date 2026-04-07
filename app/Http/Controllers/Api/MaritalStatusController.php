<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MaritalStatus;
use Illuminate\Http\Request;

class MaritalStatusController extends Controller
{
    public function index()
    {
        return response()->json(MaritalStatus::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate(['nome' => 'required|string|unique:marital_statuses']);
        $status = MaritalStatus::create($validated);
        return response()->json($status, 201);
    }
}
