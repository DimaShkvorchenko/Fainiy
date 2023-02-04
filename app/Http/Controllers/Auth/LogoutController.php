<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Cookie;

class LogoutController extends Controller
{
    /**
     * Logout (only if using cookies)
     * @return [type]
     */
    public function logout()
    {
        return response(['message' => 'success'])->withCookie(Cookie::forget('jwt'));
    }
}
