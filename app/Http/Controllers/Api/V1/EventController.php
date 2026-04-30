<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreEventRequest;
use App\Http\Requests\Api\V1\UpdateEventRequest;
use App\Http\Resources\V1\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Builder;

class EventController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Event::with('eventable');

        if ($request->boolean('available')) {
            $query->whereHasMorph('eventable', [\App\Models\Camping::class, \App\Models\Festival::class], function (Builder $query, string $type) {
                $now = now();
                if ($type === \App\Models\Camping::class) {
                    $query->where(function ($q) use ($now) {
                        $q->where(function ($subQ) use ($now) {
                            $subQ->where('raffle_camper_subscription_start_date', '<=', $now)
                                 ->where('raffle_camper_subscription_end_date', '>=', $now);
                        })->orWhere(function ($subQ) use ($now) {
                            $subQ->where('camper_registration_start_date', '<=', $now)
                                 ->where('camper_registration_end_date', '>=', $now);
                        });
                    });
                } elseif ($type === \App\Models\Festival::class) {
                    $query->where('sale_start_date', '<=', $now);
                }
            });
        }

        return EventResource::collection($query->paginate());
    }

    public function store(StoreEventRequest $request): EventResource
    {
        return EventResource::make(Event::create($request->validated()));
    }

    public function show(Event $event): EventResource
    {
        return EventResource::make($event->load('eventable'));
    }

    public function update(UpdateEventRequest $request, Event $event): EventResource
    {
        $event->update($request->validated());

        return EventResource::make($event);
    }

    public function destroy(Event $event): Response
    {
        $event->delete();

        return response()->noContent();
    }
}
