<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreRoleRequest;
use App\Http\Requests\Api\V1\UpdateRoleRequest;
use App\Http\Resources\V1\RoleResource;
use App\Models\Role;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class RoleController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return RoleResource::collection(Role::paginate());
    }

    public function store(StoreRoleRequest $request): RoleResource
    {
        return RoleResource::make(Role::create($request->validated()));
    }

    public function show(Role $role): RoleResource
    {
        return RoleResource::make($role);
    }

    public function update(UpdateRoleRequest $request, Role $role): RoleResource
    {
        $role->update($request->validated());

        return RoleResource::make($role);
    }

    public function destroy(Role $role): Response
    {
        $role->delete();

        return response()->noContent();
    }
}
