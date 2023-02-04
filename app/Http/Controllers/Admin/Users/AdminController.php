<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\SearchRequest;
use App\Services\Search\UserSearch;
use App\Http\Resources\{AdminCollection, AdminResource};
use App\Models\User;

class AdminController extends BaseUserController
{
    /**
     * Display a listing of the resource.
     *
     * @param  SearchRequest  $request
     * @param  UserSearch  $search
     * @return AdminCollection
     */
    public function index(SearchRequest $request, UserSearch $search): AdminCollection
    {
        $admin = $search->getQuery(User::admin(), $request->validated());
        return new AdminCollection($admin->paginate($request->per_page ?? 10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param RegisterRequest $request
     * @return AdminResource
     */
    public function store(RegisterRequest $request): AdminResource
    {
        $admin = $this->service->createAdmin($request->validated());
        $admin->assignRole('Admin');
        return new AdminResource($admin);
    }
}
