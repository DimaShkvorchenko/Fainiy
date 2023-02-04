<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Requests\User\UpdateTelegramRequest;
use App\Http\Resources\TelegramResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TelegramController extends BaseUserController
{
    /**
     * @param User $telegram
     * @return TelegramResource
     */
    public function show(User $telegram): TelegramResource
    {
        parent::validateUserType($telegram, User::CLIENT_TYPE);
        return new TelegramResource($telegram);
    }

    /**
     * @param UpdateTelegramRequest $request
     * @param User $telegram
     * @return JsonResponse
     */
    public function update(UpdateTelegramRequest $request, User $telegram): JsonResponse
    {
        parent::validateUserType($telegram, User::CLIENT_TYPE);
        $telegram->update($request->safe()->only(['telegram']));
        return (new TelegramResource($telegram))->response()->setStatusCode(Response::HTTP_ACCEPTED);
    }
}
