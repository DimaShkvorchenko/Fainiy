<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserCollection;
use App\Http\Responses\UserResponse;
use App\Http\Requests\SearchRequest;
use App\Services\Search\UserSearch;
use App\Models\User;
use App\Services\RegisterService;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseUserController extends Controller
{
    public function __construct(
        protected RegisterService $service,
        protected UserResponse $response,
    )
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @param  SearchRequest  $request
     * @param  UserSearch  $search
     * @return object
     */
    public function index(SearchRequest $request, UserSearch $search): object
    {
        $users = $search->getQuery(User::query(), $request->validated());
        return new UserCollection($users->paginate($request->per_page ?? 10));
    }

    /**
     * @param User $user
     * @param $userType
     */
    protected function validateUserType(User $user, $userType)
    {
        if ($user->account_type != $userType) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'User is not of type' . User::TYPES[$userType]);
        }
    }
}
