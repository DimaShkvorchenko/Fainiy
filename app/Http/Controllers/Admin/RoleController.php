<?php

namespace App\Http\Controllers\Admin;

use App\Services\SearchService;
use App\Http\Requests\SearchRequest;
use App\Services\Search\RoleSearch;
use App\Http\Requests\{StoreRoleRequest, UpdateRoleRequest};
use Spatie\Permission\Models\Permission;
use App\Http\Resources\RoleCollection;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;
use DB;
use Symfony\Component\HttpFoundation\Response;

class RoleController extends BaseCollectionController
{
    function __construct(protected SearchService $search)
    {
        $this->middleware('permission:role-list|role-show|role-store|role-update|role-delete', ['only' => ['index']]);
        $this->middleware('permission:role-show', ['only' => ['show']]);
        $this->middleware('permission:role-store', ['only' => ['store']]);
        $this->middleware('permission:role-update', ['only' => ['update']]);
        $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a filtered listing of the resource.
     *
     * @param  SearchRequest  $request
     * @param  RoleSearch  $search
     * @return RoleCollection
     */
    public function index(SearchRequest $request, RoleSearch $search): RoleCollection
    {
        $roles = $search->getQuery(Role::query(), $request->validated());
        return new RoleCollection($roles->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRoleRequest  $request
     * @return Response
     */

    public function store(StoreRoleRequest $request): Response
    {
        $role = Role::create(['guard_name' => 'web', 'name' => $request->input('name')]);
        $role->syncPermissions($request->input('permission'));
        return response($role)->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $role = Role::find($id);
        $permission = Permission::get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();

        return ['role' => $role, 'rolePermissions' => $rolePermissions, 'permission' => $permission];
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRateRequest  $request
     * @param  Role  $role
     * @return Response
     */

    public function update(UpdateRoleRequest $request, $id)
    {
        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();
        $role->syncPermissions($request->input('permission'));
        return response($role)->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Role  $rate
     * @return JsonResponse
     */
    public function destroy(Role $role): JsonResponse
    {
        return parent::delete($role);
    }
}
