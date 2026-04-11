<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreCampingRequest;
use App\Http\Requests\Api\V1\UpdateCampingRequest;
use App\Http\Resources\V1\CampingResource;
use App\Models\Camping;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CampingController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return CampingResource::collection(Camping::paginate());
    }

    public function store(StoreCampingRequest $request): CampingResource
    {
        return CampingResource::make(Camping::create($request->validated()));
    }

    public function show(Camping $camping): CampingResource
    {
        return CampingResource::make($camping);
    }

    public function update(UpdateCampingRequest $request, Camping $camping): CampingResource
    {
        $camping->update($request->validated());

        return CampingResource::make($camping);
    }

    public function destroy(Camping $camping): Response
    {
        $camping->delete();

        return response()->noContent();
    }
}
