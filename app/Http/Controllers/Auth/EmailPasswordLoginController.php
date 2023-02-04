<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Auth\EmailPasswordLoginRequest;
use Illuminate\Support\Facades\Auth;

class EmailPasswordLoginController extends Controller
{
    /**
     * Login by email and password.
     *
     * @param EmailPasswordLoginRequest $request
     * @return Response|JsonResponse
     */
    public function login(EmailPasswordLoginRequest $request): Response|JsonResponse
    {
        if (!Auth::attempt($request->validated())) {
            return response(['message' => 'Invalid credential'], Response::HTTP_UNAUTHORIZED);
        }
        $jwt = Auth::user()->createToken('token', ['admin'])->plainTextToken;

        return response()->json(compact('jwt'));
    }
}
