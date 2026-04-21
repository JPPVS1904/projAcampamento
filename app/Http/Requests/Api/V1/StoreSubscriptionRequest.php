<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\SubscriptionType;
use App\Models\Camping;
use App\Models\Event;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'subscription_date' => ['required', 'date'],
            'subscription_type' => ['required', Rule::enum(SubscriptionType::class)],
            'was_selected' => ['required', 'boolean'],
            'substitute_position' => ['required', 'integer', 'min:0'],
            'paid_the_fee' => ['required', 'boolean'],
            'is_quitter' => ['required', 'boolean'],
            'payment_code' => ['required', 'string', 'max:255'],
            'qrcode_data' => ['required', 'string', 'max:255'],
            'used_qrcode' => ['required', 'boolean'],
            'selection_method_id' => ['required', 'integer', 'exists:selection_methods,id'],
            'user_id' => ['required', 'integer', 'exists:users,id', Rule::in([$this->user()?->id])],
            'spouse_id' => ['nullable', 'integer', 'exists:users,id'],
            'event_id' => ['required', 'integer', 'exists:events,id'],
            'sector_id' => ['nullable', 'integer', 'exists:sectors,id'],
        ];
    }

    /**
     * @return array<callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $user = $this->user();
                $eventId = $this->input('event_id');

                if (!$user || !$eventId) {
                    return;
                }

                $event = Event::with('eventable')->find($eventId);

                if (!$event) {
                    return;
                }

                $alreadySubscribed = $user->subscriptions()->where('event_id', $event->id)->exists();

                if ($alreadySubscribed) {
                    $validator->errors()->add('user_id', 'O usuário já está inscrito neste evento.');
                }

                if ($event->eventable instanceof Camping) {
                    $camping = $event->eventable;
                    $subscriptionType = $this->input('subscription_type');
                    $now = now();

                    if ($subscriptionType === SubscriptionType::Camper->value) {
                        if ($camping->camper_registration_start_date && $now->lt($camping->camper_registration_start_date)) {
                            $validator->errors()->add('event_id', 'As inscrições para campistas ainda não começaram.');
                        }
                        if ($camping->camper_registration_end_date && $now->gt($camping->camper_registration_end_date)) {
                            $validator->errors()->add('event_id', 'As inscrições para campistas já foram encerradas.');
                        }
                    } elseif ($subscriptionType === SubscriptionType::Servant->value) {
                        if ($camping->servant_registration_start_date && $now->lt($camping->servant_registration_start_date)) {
                            $validator->errors()->add('event_id', 'As inscrições para servos ainda não começaram.');
                        }
                        if ($camping->servant_registration_end_date && $now->gt($camping->servant_registration_end_date)) {
                            $validator->errors()->add('event_id', 'As inscrições para servos já foram encerradas.');
                        }
                    }

                    if ($user->birthday) {
                        $age = $user->birthday->age;

                        if ($age < $camping->minimal_age) {
                            $validator->errors()->add('user_id', 'O usuário não possui a idade mínima para participar deste acampamento.');
                        }

                        if ($age > $camping->maximal_age) {
                            $validator->errors()->add('user_id', 'O usuário excede a idade máxima para participar deste acampamento.');
                        }
                    }
                }
            }
        ];
    }
}
