<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Camping;
use App\Models\CampingPreRegistration;
use App\Models\PreRegistration;
use App\Models\Sector;
use App\Models\User;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        // Pega todos os campamentos criados
        $activities = Activity::where('activitable_type', 'App\Models\Camping')->get();

        // Garante que existam mais usuários normais
        $users = User::factory()->count(50)->create([
            'is_admin' => false,
            'is_counselor' => false,
        ]);

        // Assumindo que os usuários administradores do UserSeeder possuem e-mails que terminam em @teste.com
        $adminCasais = User::where('email', 'casais@teste.com')->first();
        $conjugeCasais = User::where('email', 'conjuge_casais@teste.com')->first();
        
        $sectors = Sector::all();
        if ($sectors->count() < 2) {
            $sectors = Sector::factory()->count(2)->create();
        }

        $questions = Question::all();

        $chunkIndex = 0;
        foreach ($activities as $activity) {
            $camping = $activity->activitable;
            $usersChunk = $users->slice($chunkIndex * 10, 10);
            $chunkIndex++;
            if ($chunkIndex >= 5) $chunkIndex = 0;

            // 1. Inscrição paga, com formulário respondido, Servo
            $this->createSubscription(
                $usersChunk->values()->get(0),
                $activity,
                'Servo',
                true, // fee paid
                true, // selected
                false, // quitter
                $sectors->get(0)->id,
                $sectors->get(1)->id,
                null, // spouse
                true, // answer form
                $questions
            );

            // 2. Inscrição não paga, sorteado, Campista
            $this->createSubscription(
                $usersChunk->values()->get(1),
                $activity,
                'Campista',
                false, // fee paid
                true, // selected
                false, // quitter
                null,
                null,
                null,
                false,
                $questions
            );

            // 3. Inscrição paga, não sorteado, Campista
            $this->createSubscription(
                $usersChunk->values()->get(2),
                $activity,
                'Campista',
                true, // fee paid
                false, // selected
                false, // quitter
                null,
                null,
                null,
                false,
                $questions
            );

            // 4. Inscrição desistente (Servo)
            $this->createSubscription(
                $usersChunk->values()->get(3),
                $activity,
                'Servo',
                true, // fee paid
                true, // selected
                true, // quitter
                $sectors->get(0)->id,
                $sectors->get(1)->id,
                null,
                true,
                $questions
            );

            // 5. Inscrição Campista, selecionado, formulário devolvido
            $subReturned = $this->createSubscription(
                $usersChunk->values()->get(4),
                $activity,
                'Campista',
                true, // fee paid
                true, // selected
                false, // quitter
                null,
                null,
                null,
                true,
                $questions
            );
            if ($subReturned) {
                $subReturned->update([
                    'is_form_returned' => true,
                    'return_instructions' => 'Por favor, melhore sua resposta.',
                    'returned_fields' => [$questions->first()->id ?? 1],
                ]);
            }

            // 6. Masculina vs Feminina (Usando sexos específicos)
            $userM = $usersChunk->values()->get(5);
            $userM->update(['sex' => 'M']);
            $this->createSubscription($userM, $activity, 'Campista', true, true, false, null, null, null, true, $questions);

            $userF = $usersChunk->values()->get(6);
            $userF->update(['sex' => 'F']);
            $this->createSubscription($userF, $activity, 'Campista', true, true, false, null, null, null, true, $questions);

            // Casal (Apenas se for Sênior ou Casais)
            if ($activity->category && in_array($activity->category->name, ['Acampamento Sênior', 'Acampamento Casais'])) {
                if ($adminCasais && $conjugeCasais) {
                    $this->createSubscription(
                        $adminCasais,
                        $activity,
                        'Campista',
                        true,
                        true,
                        false,
                        null,
                        null,
                        $conjugeCasais->id,
                        true,
                        $questions
                    );
                }
            }
        }
    }

    private function createSubscription($user, $activity, $type, $isFeePaid, $isSelected, $isQuitter, $sector1, $sector2, $spouseId, $answerForm, $questions)
    {
        if (!$user) return null;

        $campingPreReg = CampingPreRegistration::create([
            'substitute_position' => $isSelected ? null : rand(1, 20),
            'is_quitter' => $isQuitter,
            'selection_method_id' => $isSelected ? 1 : null, // 1 = Sorteio
            'spouse_id' => $spouseId,
            'sector_id' => $sector1,
            'sector2_id' => $sector2,
            'is_approved' => true,
            'has_taken_new_photo' => true,
        ]);

        $preReg = PreRegistration::create([
            'subscription_type' => $type,
            'is_fee_paid' => $isFeePaid,
            'payment_code' => $isFeePaid ? Str::random(10) : null,
            'qrcode_data' => Str::uuid(),
            'is_qrcode_used' => rand(0, 1) == 1,
            'user_id' => $user->id,
            'activity_id' => $activity->id,
            'camping_pre_registration_id' => $campingPreReg->id,
            'is_form_returned' => false,
            'return_instructions' => null,
            'returned_fields' => [],
        ]);

        if ($answerForm && $questions->count() > 0) {
            foreach ($questions as $question) {
                Answer::create([
                    'answer' => 'Resposta gerada automaticamente ' . Str::random(5),
                    'pre_registration_id' => $preReg->id,
                    'question_id' => $question->id,
                ]);
            }
        }

        return $preReg;
    }
}
