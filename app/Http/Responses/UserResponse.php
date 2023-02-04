<?php

namespace App\Http\Responses;

use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UserResponse
{
    /**
     * @param User $user
     * @return JsonResponse
     */
    public function updated(User $user): JsonResponse
    {
        return (new UserResource($user))->response()->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * @param User $user
     * @return UserResource
     */
    public function single(User $user): UserResource
    {
        return new UserResource($user);
    }

    /**
     * @param Collection $users
     * @return UserCollection
     */
    public function list(Collection $users): UserCollection
    {
        return new UserCollection($users->paginate(10));
    }
}
