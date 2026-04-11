<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreMaritalStatusRequest;
use App\Http\Requests\Api\V1\UpdateMaritalStatusRequest;
use App\Http\Resources\V1\MaritalStatusResource;
use App\Models\MaritalStatus;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class MaritalStatusController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return MaritalStatusResource::collection(MaritalStatus::paginate());
    }

    public function store(StoreMaritalStatusRequest $request): MaritalStatusResource
    {
        return MaritalStatusResource::make(MaritalStatus::create($request->validated()));
    }

    public function show(MaritalStatus $maritalStatus): MaritalStatusResource
    {
        return MaritalStatusResource::make($maritalStatus);
    }

    public function update(UpdateMaritalStatusRequest $request, MaritalStatus $maritalStatus): MaritalStatusResource
    {
        $maritalStatus->update($request->validated());

        return MaritalStatusResource::make($maritalStatus);
    }

    public function destroy(MaritalStatus $maritalStatus): Response
    {
        $maritalStatus->delete();

        return response()->noContent();
    }
}
