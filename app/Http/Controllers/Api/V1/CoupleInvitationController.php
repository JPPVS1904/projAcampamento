<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Activity;
use App\Models\CampingPreRegistration;
use App\Models\CoupleInvitation;
use App\Models\InboxMessage;
use App\Models\PreRegistration;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CoupleInvitationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'activity_id' => ['required', 'integer', 'exists:activities,id'],
            'spouse_cpf' => ['required', 'string'],
        ]);

        $inviter = Auth::user();
        $activityId = $validated['activity_id'];

        $spouseCpfClean = preg_replace('/\D/', '', $validated['spouse_cpf']);
        if (strlen($spouseCpfClean) !== 11) {
            throw ValidationException::withMessages([
                'spouse_cpf' => ['CPF do cônjuge inválido.']
            ]);
        }

        // Buscar usuário cônjuge
        $invitee = User::whereRaw("REGEXP_REPLACE(cpf, '[^0-9]', '') = ?", [$spouseCpfClean])->first();

        if (!$invitee) {
            throw ValidationException::withMessages([
                'spouse_cpf' => ['Cônjuge não encontrado no sistema. Peça para que ele(a) se cadastre primeiro.']
            ]);
        }

        if ($inviter->id === $invitee->id) {
            throw ValidationException::withMessages([
                'spouse_cpf' => ['Você não pode convidar a si mesmo.']
            ]);
        }

        if ($inviter->sex === $invitee->sex) {
            throw ValidationException::withMessages([
                'spouse_cpf' => ['Inscrição como casal só é permitida para casais heterossexuais.']
            ]);
        }

        // Verificar se algum dos dois já está inscrito
        $inviterAlreadySubscribed = PreRegistration::where('user_id', $inviter->id)
            ->where('activity_id', $activityId)
            ->exists();
        if ($inviterAlreadySubscribed) {
            throw ValidationException::withMessages([
                'activity_id' => ['Você já possui uma inscrição para esta atividade.']
            ]);
        }

        $inviteeAlreadySubscribed = PreRegistration::where('user_id', $invitee->id)
            ->where('activity_id', $activityId)
            ->exists();
        if ($inviteeAlreadySubscribed) {
            throw ValidationException::withMessages([
                'spouse_cpf' => ['Seu cônjuge já possui uma inscrição para esta atividade.']
            ]);
        }

        // Verificar convites pendentes
        $pendingInvitation = CoupleInvitation::where('activity_id', $activityId)
            ->where(function ($q) use ($inviter, $invitee) {
                $q->where(function ($q1) use ($inviter, $invitee) {
                    $q1->where('inviter_id', $inviter->id)->where('invitee_id', $invitee->id);
                })->orWhere(function ($q2) use ($inviter, $invitee) {
                    $q2->where('inviter_id', $invitee->id)->where('invitee_id', $inviter->id);
                });
            })
            ->where('status', 'pending')
            ->exists();

        if ($pendingInvitation) {
            throw ValidationException::withMessages([
                'spouse_cpf' => ['Já existe um convite pendente entre vocês para esta atividade.']
            ]);
        }

        return DB::transaction(function () use ($inviter, $invitee, $activityId) {
            $invitation = CoupleInvitation::create([
                'inviter_id' => $inviter->id,
                'invitee_id' => $invitee->id,
                'activity_id' => $activityId,
                'status' => 'pending',
            ]);

            $activity = Activity::find($activityId);
            $activityName = $activity ? $activity->name : 'Acampamento';

            InboxMessage::create([
                'user_id' => $invitee->id,
                'title' => 'Convite para Inscrição de Casal',
                'content' => "{$inviter->name} convidou você para participarem juntos do {$activityName}.",
                'action_type' => 'couple_invitation',
                'action_id' => $invitation->id,
            ]);

            return response()->json([
                'message' => 'Convite enviado com sucesso.',
                'data' => $invitation
            ], 201);
        });
    }

    public function accept($id)
    {
        $invitation = CoupleInvitation::findOrFail($id);
        $user = Auth::user();

        if ($invitation->invitee_id !== $user->id) {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }

        if ($invitation->status !== 'pending') {
            return response()->json(['message' => 'Este convite já foi processado.'], 400);
        }

        return DB::transaction(function () use ($invitation) {
            $invitation->update(['status' => 'accepted']);

            $campingPreRegInviter = CampingPreRegistration::create([
                'substitute_position' => null,
                'is_quitter' => false,
                'selection_method_id' => null,
                'spouse_id' => $invitation->invitee_id,
            ]);

            $campingPreRegInvitee = CampingPreRegistration::create([
                'substitute_position' => null,
                'is_quitter' => false,
                'selection_method_id' => null,
                'spouse_id' => $invitation->inviter_id,
            ]);

            $preRegInviter = PreRegistration::create([
                'subscription_type' => 'Campista',
                'is_fee_paid' => false,
                'payment_code' => null,
                'qrcode_data' => null,
                'is_qrcode_used' => false,
                'user_id' => $invitation->inviter_id,
                'activity_id' => $invitation->activity_id,
                'camping_pre_registration_id' => $campingPreRegInviter->id,
            ]);

            $preRegInvitee = PreRegistration::create([
                'subscription_type' => 'Campista',
                'is_fee_paid' => false,
                'payment_code' => null,
                'qrcode_data' => null,
                'is_qrcode_used' => false,
                'user_id' => $invitation->invitee_id,
                'activity_id' => $invitation->activity_id,
                'camping_pre_registration_id' => $campingPreRegInvitee->id,
            ]);

            $activity = Activity::find($invitation->activity_id);
            $activityName = $activity ? $activity->name : 'Acampamento';

            InboxMessage::create([
                'user_id' => $invitation->inviter_id,
                'title' => 'Convite Aceito!',
                'content' => "Seu cônjuge aceitou o convite para o {$activityName}. Vocês já estão inscritos! Para finalizar, prossiga com o pagamento da sua inscrição.",
                'action_type' => 'pay_subscription',
                'action_id' => $preRegInviter->id,
            ]);

            InboxMessage::where('action_type', 'couple_invitation')
                ->where('action_id', $invitation->id)
                ->update(['action_type' => null]);

            return response()->json([
                'message' => 'Convite aceito e inscrições criadas com sucesso!',
                'data' => $preRegInvitee->load(['activity.activitable', 'activity.category', 'campingPreRegistration'])
            ]);
        });
    }

    public function reject($id)
    {
        $invitation = CoupleInvitation::findOrFail($id);
        $user = Auth::user();

        if ($invitation->invitee_id !== $user->id) {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }

        if ($invitation->status !== 'pending') {
            return response()->json(['message' => 'Este convite já foi processado.'], 400);
        }

        return DB::transaction(function () use ($invitation) {
            $invitation->update(['status' => 'rejected']);

            InboxMessage::create([
                'user_id' => $invitation->inviter_id,
                'title' => 'Convite Recusado',
                'content' => "Seu cônjuge recusou o convite para a inscrição em casal.",
            ]);

            InboxMessage::where('action_type', 'couple_invitation')
                ->where('action_id', $invitation->id)
                ->update(['action_type' => null]);

            return response()->json(['message' => 'Convite recusado com sucesso.']);
        });
    }
}
