<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Answer::query();
        if ($request->has('pre_registration_id')) {
            $query->where('pre_registration_id', $request->pre_registration_id);
        }
        return response()->json(['data' => $query->get()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pre_registration_id' => 'required|integer|exists:pre_registrations,id',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|integer|exists:questions,id',
            'answers.*.answer' => 'required|string',
        ]);

        $preRegId = $validated['pre_registration_id'];
        
        $preReg = \App\Models\PreRegistration::findOrFail($preRegId);
        if ($preReg->user_id !== $request->user()->id && !$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        foreach ($validated['answers'] as $ansData) {
            Answer::updateOrCreate(
                [
                    'pre_registration_id' => $preRegId,
                    'question_id' => $ansData['question_id'],
                ],
                ['answer' => $ansData['answer']]
            );
        }

        // Garante que o registro na tabela camping_pre_registrations exista para que apareça na avaliação
        if (!$preReg->campingPreRegistration) {
            $preReg->campingPreRegistration()->create([
                'is_approved' => false,
                'is_quitter' => false,
                'selection_method_id' => null
            ]);
        }

        return response()->json(['message' => 'Respostas salvas com sucesso.']);
    }

    public function show(Answer $answer)
    {
        return response()->json(['data' => $answer]);
    }

    public function update(Request $request, Answer $answer)
    {
        // Not commonly used directly, usually update via store() updateOrCreate
    }

    public function destroy(Answer $answer)
    {
        $answer->delete();
        return response()->noContent();
    }
}
