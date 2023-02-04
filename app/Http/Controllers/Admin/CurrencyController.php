<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\{
    SearchCurrencyRequest,
    StoreCurrencyRequest,
    UpdateCurrencyRequest,
    BulkDestroyCurrencyRequest
};
use App\Services\Search\CurrencySearch;
use App\Http\Resources\{CurrencyCollection, CurrencyResource};
use App\Models\Currency;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CurrencyController extends BaseCollectionController
{
    /**
     * Display a listing of the resource.
     *
     * @param  SearchCurrencyRequest  $request
     * @param  CurrencySearch  $search
     * @return CurrencyCollection
     */
    public function index(SearchCurrencyRequest $request, CurrencySearch $search): CurrencyCollection
    {
        $currencies = $search->getQuery(Currency::query(), $request->validated());
        return new CurrencyCollection($currencies->paginate($request->per_page ?? 10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreCurrencyRequest  $request
     * @return CurrencyResource
     */
    public function store(StoreCurrencyRequest $request): CurrencyResource
    {
        $currency = Currency::create($request->validated());
        return new CurrencyResource($currency);
    }

    /**
     * Display the specified resource.
     *
     * @param  Currency  $currency
     * @return CurrencyResource
     */
    public function show(Currency $currency): CurrencyResource
    {
        return new CurrencyResource($currency);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateCurrencyRequest  $request
     * @param  Currency  $currency
     * @return JsonResponse
     */
    public function update(UpdateCurrencyRequest $request, Currency $currency): JsonResponse
    {
        $currency->update($request->safe()->only(['iso_code', 'name']));
        return (new CurrencyResource($currency))->response()->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Currency  $currency
     * @return JsonResponse|Response
     */
    public function destroy(Currency $currency): JsonResponse|Response
    {
        if ($basicCurrency = $this->currencyService->getOrSetBasicCurrency()) {
            if ($currency->id == $basicCurrency->id) {
                abort(response(['message' => 'Currency setted as basic cannot be deleted'], Response::HTTP_UNPROCESSABLE_ENTITY));
            }
        }

        return parent::delete($currency);
    }

    /**
     * Remove resources from storage by array of ID's.
     *
     * @param  BulkDestroyCurrencyRequest  $request
     * @return JsonResponse
     */
    public function bulkDestroy(BulkDestroyCurrencyRequest $request): JsonResponse
    {
        return parent::bulkDelete(Currency::query(), $request->validated());
    }
}
