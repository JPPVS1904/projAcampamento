<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreUserRequest;
use App\Http\Requests\Api\V1\UpdateUserRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return UserResource::collection(User::with('maritalStatus')->paginate());
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = User::create($request->validated());
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'data' => UserResource::make($user),
            'token' => $token,
        ], 201);
    }

    public function show(User $user): UserResource
    {
        return UserResource::make($user->load('maritalStatus'));
    }

    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        $user->update($request->validated());

        return UserResource::make($user);
    }

    public function destroy(User $user): Response
    {
        $user->delete();

        return response()->noContent();
    }
}
