<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreSelectionMethodRequest;
use App\Http\Requests\Api\V1\UpdateSelectionMethodRequest;
use App\Http\Resources\V1\SelectionMethodResource;
use App\Models\SelectionMethod;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class SelectionMethodController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return SelectionMethodResource::collection(SelectionMethod::paginate());
    }

    public function store(StoreSelectionMethodRequest $request): SelectionMethodResource
    {
        return SelectionMethodResource::make(SelectionMethod::create($request->validated()));
    }

    public function show(SelectionMethod $selectionMethod): SelectionMethodResource
    {
        return SelectionMethodResource::make($selectionMethod);
    }

    public function update(UpdateSelectionMethodRequest $request, SelectionMethod $selectionMethod): SelectionMethodResource
    {
        $selectionMethod->update($request->validated());

        return SelectionMethodResource::make($selectionMethod);
    }

    public function destroy(SelectionMethod $selectionMethod): Response
    {
        $selectionMethod->delete();

        return response()->noContent();
    }
}
