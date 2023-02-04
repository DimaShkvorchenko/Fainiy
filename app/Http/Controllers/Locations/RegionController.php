<?php

namespace App\Http\Controllers\Locations;

use App\Http\Controllers\Admin\BaseCollectionController;
use App\Http\Requests\SearchRequest;
use App\Services\Search\RegionSearch;
use App\Http\Requests\Locations\{StoreRegionRequest, UpdateRegionRequest, BulkDestroyRegionRequest};
use App\Http\Resources\Locations\{RegionCollection, RegionResource};
use App\Models\Locations\Region;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RegionController extends BaseCollectionController
{
    /**
     * Display a listing of the resource.
     *
     * @param  SearchRequest  $request
     * @param  RegionSearch  $search
     * @return RegionCollection
     */
    public function index(SearchRequest $request, RegionSearch $search): RegionCollection
    {
        $regions = $search->getQuery(Region::query(), $request->validated());
        return new RegionCollection($regions->paginate($request->per_page ?? 10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRegionRequest  $request
     * @return RegionResource
     */
    public function store(StoreRegionRequest $request): RegionResource
    {
        $region = Region::create($request->validated());
        return new RegionResource($region);
    }

    /**
     * Display the specified resource.
     *
     * @param  Region  $region
     * @return RegionResource
     */
    public function show(Region $region): RegionResource
    {
        return new RegionResource($region);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRegionRequest  $request
     * @param  Region  $region
     * @return JsonResponse
     */
    public function update(UpdateRegionRequest $request, Region $region): JsonResponse
    {
        $region->update($request->safe()->only(['country_id', 'name']));
        return (new RegionResource($region))->response()->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Region  $region
     * @return JsonResponse
     */
    public function destroy(Region $region): JsonResponse
    {
        return parent::delete($region);
    }

    /**
     * Remove resources from storage by array of ID's.
     *
     * @param  BulkDestroyRegionRequest  $request
     * @return JsonResponse
     */
    public function bulkDestroy(BulkDestroyRegionRequest $request): JsonResponse
    {
        return parent::bulkDelete(Region::query(), $request->validated());
    }
}
