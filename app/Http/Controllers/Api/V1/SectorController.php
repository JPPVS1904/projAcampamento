<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreSectorRequest;
use App\Http\Requests\Api\V1\UpdateSectorRequest;
use App\Http\Resources\V1\SectorResource;
use App\Models\Sector;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class SectorController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return SectorResource::collection(Sector::paginate());
    }

    public function store(StoreSectorRequest $request): SectorResource
    {
        return SectorResource::make(Sector::create($request->validated()));
    }

    public function show(Sector $sector): SectorResource
    {
        return SectorResource::make($sector);
    }

    public function update(UpdateSectorRequest $request, Sector $sector): SectorResource
    {
        $sector->update($request->validated());

        return SectorResource::make($sector);
    }

    public function destroy(Sector $sector): Response
    {
        $sector->delete();

        return response()->noContent();
    }
}
