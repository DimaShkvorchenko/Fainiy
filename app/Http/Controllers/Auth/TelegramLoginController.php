<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Auth\TelegramLoginRequest;
use App\Models\User;

class TelegramLoginController extends Controller
{
    /**
     * Login by Telegram.
     *
     * @param TelegramLoginRequest $request
     * @return Response|JsonResponse
     */
    public function login(TelegramLoginRequest $request): Response|JsonResponse
    {
        $fields = $request->validated();
        if (!$user = User::where('telegram', $fields['username'])->oldest()->first()) {
            return response(['message' => 'Invalid telegram credential'], Response::HTTP_UNAUTHORIZED);
        }
        $jwt = $user->createToken('token', ['admin'])->plainTextToken;

        return response()->json(compact('jwt'));
    }
}
