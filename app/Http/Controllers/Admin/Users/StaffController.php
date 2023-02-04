<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Requests\SearchRequest;
use App\Services\Search\UserSearch;
use App\Http\Requests\User\{StoreStaffRequest, UpdateStaffRequest, BulkDestroyStaffRequest};
use App\Http\Resources\{StaffCollection, StaffResource};
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class StaffController extends BaseUserController
{
    /**
     * Display a listing of the resource.
     *
     * @param  SearchRequest  $request
     * @param  UserSearch  $search
     * @return StaffCollection
     */
    public function index(SearchRequest $request, UserSearch $search): StaffCollection
    {
        $staff = $search->getQuery(User::staff(), $request->validated());
        return new StaffCollection($staff->paginate($request->per_page ?? 10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreStaffRequest $request
     * @return StaffResource
     */
    public function store(StoreStaffRequest $request): StaffResource
    {
        $staff = $this->service->createStaff($request->validated());
        return new StaffResource($staff);
    }

    /**
     * Display the specified resource.
     *
     * @param User $staff
     * @return StaffResource
     */
    public function show(User $staff): StaffResource
    {
        return new StaffResource($staff);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateStaffRequest $request
     * @param User $staff
     * @return JsonResponse
     */
    public function update(UpdateStaffRequest $request, User $staff): JsonResponse
    {
        parent::validateUserType($staff, User::STAFF_TYPE);
        $staff->update($request->only(['first_name', 'last_name', 'phone']));
        return (new StaffResource($staff))->response()->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $staff
     * @return JsonResponse
     */
    public function destroy(User $staff): JsonResponse
    {
        parent::validateUserType($staff, User::STAFF_TYPE);
        return parent::delete($staff);
    }

    /**
     * Remove resources from storage by array of ID's.
     *
     * @param  BulkDestroyStaffRequest  $request
     * @return JsonResponse
     */
    public function bulkDestroy(BulkDestroyStaffRequest $request): JsonResponse
    {
        return parent::bulkDelete(User::query(), $request->validated());
    }
}
