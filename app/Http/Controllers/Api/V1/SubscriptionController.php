<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreSubscriptionRequest;
use App\Http\Requests\Api\V1\UpdateSubscriptionRequest;
use App\Http\Resources\V1\SubscriptionResource;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class SubscriptionController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Subscription::with(['event']);
        
        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }
        
        return SubscriptionResource::collection($query->paginate());
    }

    public function store(StoreSubscriptionRequest $request): SubscriptionResource
    {
        return SubscriptionResource::make(Subscription::create($request->validated()));
    }

    public function show(Subscription $subscription): SubscriptionResource
    {
        return SubscriptionResource::make(
            $subscription->load(['user', 'spouse', 'event', 'sector', 'selectionMethod'])
        );
    }

    public function update(UpdateSubscriptionRequest $request, Subscription $subscription): SubscriptionResource
    {
        $subscription->update($request->validated());

        return SubscriptionResource::make($subscription);
    }

    public function destroy(Subscription $subscription): Response
    {
        $subscription->delete();

        return response()->noContent();
    }
}
