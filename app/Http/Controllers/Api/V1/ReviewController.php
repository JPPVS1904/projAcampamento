<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PreRegistration;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * List campers that have submitted answers and need approval.
     */
    public function index(Request $request)
    {
        // Require activity_id to filter by a specific camping
        $request->validate([
            'activity_id' => 'required|integer|exists:activities,id'
        ]);

        $query = PreRegistration::with(['user', 'campingPreRegistration', 'answers.question'])
            ->where('activity_id', $request->activity_id)
            ->where('subscription_type', 'Campista')
            ->whereHas('campingPreRegistration', function ($q) {
                // Must not be a quitter, and not yet approved
                $q->where('is_quitter', false)
                  ->where('is_approved', false);
            })
            ->whereHas('answers'); // Must have submitted answers

        return response()->json(['data' => $query->get()]);
    }

    /**
     * Approve a specific pre_registration.
     */
    public function approve(Request $request, $id)
    {
        $preReg = PreRegistration::findOrFail($id);

        if ($preReg->campingPreRegistration) {
            $preReg->campingPreRegistration->update([
                'is_approved' => true
            ]);
            
            // Also notify the user
            if ($preReg->user_id) {
                \App\Models\InboxMessage::create([
                    'user_id' => $preReg->user_id,
                    'title' => 'Inscrição Confirmada!',
                    'content' => "Sua anamnese/questionário foi avaliada e sua inscrição para a atividade foi oficialmente confirmada! Prepare-se para o acampamento."
                ]);
            }
        }

        return response()->json(['message' => 'Inscrição aprovada com sucesso.']);
    }

    /**
     * Reject a specific pre_registration.
     */
    public function reject(Request $request, $id)
    {
        $preReg = PreRegistration::findOrFail($id);

        if ($preReg->campingPreRegistration) {
            // Rejeitar: remove da vaga selecionada, marca como desistente/rejeitado, apaga as respostas
            $preReg->campingPreRegistration->update([
                'is_approved' => false,
                'is_quitter' => true,
                'selection_method_id' => null
            ]);
            
            // Delete answers so they can't be reviewed again
            $preReg->answers()->delete();

            // Notify user
            if ($preReg->user_id) {
                \App\Models\InboxMessage::create([
                    'user_id' => $preReg->user_id,
                    'title' => 'Inscrição Rejeitada',
                    'content' => "Sua inscrição para a atividade foi avaliada pela equipe e, infelizmente, não foi aprovada neste momento."
                ]);
            }
        }

        return response()->json(['message' => 'Inscrição rejeitada com sucesso.']);
    }
}
