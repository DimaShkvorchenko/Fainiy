<?php

namespace App\Http\Controllers\Locations;

use App\Http\Controllers\Admin\BaseCollectionController;
use App\Services\Search\{BranchSearch, BranchToWalletsSearch};
use App\Http\Requests\{SearchRequest, SearchBranchesToWalletsRequest};
use App\Http\Requests\Locations\{StoreBranchRequest, UpdateBranchRequest, BulkDestroyBranchRequest};
use App\Http\Resources\Locations\{BranchCollection, BranchResource, BranchToWalletsCollection};
use App\Models\Locations\Branch;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BranchController extends BaseCollectionController
{
    /**
     * Display a listing of the resource.
     *
     * @param  SearchRequest  $request
     * @param  BranchSearch  $search
     * @return BranchCollection
     */
    public function index(SearchRequest $request, BranchSearch $search): BranchCollection
    {
        $branches = $search->getQuery(Branch::query(), $request->validated());
        return new BranchCollection($branches->paginate($request->per_page ?? 10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreBranchRequest  $request
     * @return BranchResource
     */
    public function store(StoreBranchRequest $request): BranchResource
    {
        $branch = Branch::create($request->validated());
        return new BranchResource($branch);
    }

    /**
     * Display the specified resource.
     *
     * @param  Branch  $branch
     * @return BranchResource
     */
    public function show(Branch $branch): BranchResource
    {
        return new BranchResource($branch);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateBranchRequest  $request
     * @param  Branch  $branch
     * @return JsonResponse
     */
    public function update(UpdateBranchRequest $request, Branch $branch): JsonResponse
    {
        $branch->update($request->safe()->only(['city_id', 'name']));
        return (new BranchResource($branch))->response()->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Branch  $branch
     * @return JsonResponse
     */
    public function destroy(Branch $branch): JsonResponse
    {
        return parent::delete($branch);
    }

    /**
     * Remove resources from storage by array of ID's.
     *
     * @param  BulkDestroyBranchRequest  $request
     * @return JsonResponse
     */
    public function bulkDestroy(BulkDestroyBranchRequest $request): JsonResponse
    {
        return parent::bulkDelete(Branch::query(), $request->validated());
    }

    /**
     * Display a listing of branches with relation branch > cashboxes > wallets.
     *
     * @param  SearchBranchesToWalletsRequest  $request
     * @param  BranchToWalletsSearch  $search
     * @return BranchToWalletsCollection
     */
    public function branchesToWalletsIndex(SearchBranchesToWalletsRequest $request, BranchToWalletsSearch $search): BranchToWalletsCollection
    {
        $branches = $search->getQuery(Branch::query(), $request->validated());
        return new BranchToWalletsCollection($branches->paginate($request->per_page ?? 10));
    }
}
