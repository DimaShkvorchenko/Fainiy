<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class ProfileController extends BaseUserController
{
    /**
     * @param User $profile
     * @return UserResource
     */
    public function show(User $profile): UserResource
    {
        parent::validateUserType($profile, User::CLIENT_TYPE);
        return $this->response->single($profile);
    }

    /**
     * @param UpdateUserRequest $request
     * @param User $profile
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, User $profile): JsonResponse
    {
        parent::validateUserType($profile, User::CLIENT_TYPE);
        $profile->update($request->safe()->only(['first_name', 'last_name']));
        return $this->response->updated($profile);
    }
}
