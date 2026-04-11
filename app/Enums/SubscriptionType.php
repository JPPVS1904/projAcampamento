<?php

namespace App\Enums;

enum SubscriptionType: string
{
    case Servant = 'Servo';
    case Camper = 'Campista';
    case Pending = 'Participante';
}
