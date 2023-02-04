<?php

namespace App\Http\Controllers\Locations;

use App\Http\Controllers\Admin\BaseCollectionController;
use App\Services\Search\{CitySearch, CityToRatesSearch};
use App\Http\Requests\{SearchRequest, SearchCitiesToRatesRequest};
use App\Http\Requests\Locations\{StoreCityRequest, UpdateCityRequest, BulkDestroyCityRequest};
use App\Http\Resources\Locations\{CityCollection, CityResource, CityToRatesCollection};
use App\Models\Locations\City;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CityController extends BaseCollectionController
{
    /**
     * Display a listing of the resource.
     *
     * @param  SearchRequest  $request
     * @param  CitySearch  $search
     * @return CityCollection
     */
    public function index(SearchRequest $request, CitySearch $search): CityCollection
    {
        $cities = $search->getQuery(City::query(), $request->validated());
        return new CityCollection($cities->paginate($request->per_page ?? 10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreCityRequest  $request
     * @return CityResource
     */
    public function store(StoreCityRequest $request): CityResource
    {
        $city = City::create($request->validated());
        return new CityResource($city);
    }

    /**
     * Display the specified resource.
     *
     * @param  City  $city
     * @return CityResource
     */
    public function show(City $city): CityResource
    {
        return new CityResource($city);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateCityRequest  $request
     * @param  City  $city
     * @return JsonResponse
     */
    public function update(UpdateCityRequest $request, City $city): JsonResponse
    {
        $city->update($request->safe()->only(['region_id', 'name']));
        return (new CityResource($city))->response()->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  City  $city
     * @return JsonResponse
     */
    public function destroy(City $city): JsonResponse
    {
        return parent::delete($city);
    }

    /**
     * Remove resources from storage by array of ID's.
     *
     * @param  BulkDestroyCityRequest  $request
     * @return JsonResponse
     */
    public function bulkDestroy(BulkDestroyCityRequest $request): JsonResponse
    {
        return parent::bulkDelete(City::query(), $request->validated());
    }

    /**
     * Display a listing of cities with relation city > branches > cashboxes > currencies > rates.
     *
     * @param  SearchCitiesToRatesRequest  $request
     * @param  CityToRatesSearch  $search
     * @return CityToRatesCollection
     */
    public function citiesToRatesIndex(SearchCitiesToRatesRequest $request, CityToRatesSearch $search): CityToRatesCollection
    {
        $cities = $search->getQuery(City::query(), $request->validated());
        return new CityToRatesCollection($cities->paginate($request->per_page ?? 10));
    }

}
