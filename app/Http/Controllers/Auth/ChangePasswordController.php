<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Hash;
use App\http\Requests\Auth\ChangePasswordRequest;
use Symfony\Component\HttpFoundation\Response;

class ChangePasswordController extends Controller
{
    /**
     * @param ChangePasswordRequest $request
     *
     * @return [type]
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $request->user()->update(['password' => Hash::make($request->input('password'))]);
        return response($request->user(), Response::HTTP_ACCEPTED);
    }
}
