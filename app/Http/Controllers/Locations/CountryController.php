<?php

namespace App\Http\Controllers\Locations;

use App\Http\Controllers\Admin\BaseCollectionController;
use App\Http\Requests\SearchRequest;
use App\Services\Search\CountrySearch;
use App\Http\Requests\Locations\{StoreCountryRequest, UpdateCountryRequest, BulkDestroyCountryRequest};
use App\Http\Resources\Locations\{CountryCollection, CountryResource};
use App\Models\Locations\Country;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CountryController extends BaseCollectionController
{
    /**
     * Display a listing of the resource.
     *
     * @param  SearchRequest  $request
     * @param  CountrySearch  $search
     * @return CountryCollection
     */
    public function index(SearchRequest $request, CountrySearch $search): CountryCollection
    {
        $countries = $search->getQuery(Country::query(), $request->validated());
        return new CountryCollection($countries->paginate($request->per_page ?? 10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreCountryRequest  $request
     * @return CountryResource
     */
    public function store(StoreCountryRequest $request): CountryResource
    {
        $country = Country::create($request->validated());
        return new CountryResource($country);
    }

    /**
     * Display the specified resource.
     *
     * @param  Country  $country
     * @return CountryResource
     */
    public function show(Country $country): CountryResource
    {
        return new CountryResource($country);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateCountryRequest  $request
     * @param  Country  $country
     * @return JsonResponse
     */
    public function update(UpdateCountryRequest $request, Country $country): JsonResponse
    {
        $country->update($request->safe()->only(['name']));
        return (new CountryResource($country))->response()->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Country  $country
     * @return JsonResponse
     */
    public function destroy(Country $country): JsonResponse
    {
        return parent::delete($country);
    }

    /**
     * Remove resources from storage by array of ID's.
     *
     * @param  BulkDestroyCountryRequest  $request
     * @return JsonResponse
     */
    public function bulkDestroy(BulkDestroyCountryRequest $request): JsonResponse
    {
        return parent::bulkDelete(Country::query(), $request->validated());
    }
}
