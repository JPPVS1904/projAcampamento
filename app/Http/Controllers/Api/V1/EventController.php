<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreEventRequest;
use App\Http\Requests\Api\V1\UpdateEventRequest;
use App\Http\Resources\V1\EventResource;
use App\Models\Event;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class EventController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return EventResource::collection(Event::with('eventable')->paginate());
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
