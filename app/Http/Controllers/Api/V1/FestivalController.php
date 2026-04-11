<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreFestivalRequest;
use App\Http\Requests\Api\V1\UpdateFestivalRequest;
use App\Http\Resources\V1\FestivalResource;
use App\Models\Festival;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class FestivalController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return FestivalResource::collection(Festival::paginate());
    }

    public function store(StoreFestivalRequest $request): FestivalResource
    {
        return FestivalResource::make(Festival::create($request->validated()));
    }

    public function show(Festival $festival): FestivalResource
    {
        return FestivalResource::make($festival);
    }

    public function update(UpdateFestivalRequest $request, Festival $festival): FestivalResource
    {
        $festival->update($request->validated());

        return FestivalResource::make($festival);
    }

    public function destroy(Festival $festival): Response
    {
        $festival->delete();

        return response()->noContent();
    }
}
