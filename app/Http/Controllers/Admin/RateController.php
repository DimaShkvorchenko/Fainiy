<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\{
    SearchRateRequest,
    StoreRateRequest,
    UpdateRateRequest,
    BulkDestroyRateRequest
};
use App\Services\Search\RateSearch;
use App\Http\Resources\{RateCollection, RateResource};
use App\Models\Rate;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RateController extends BaseCollectionController
{
    /**
     * Display a filtered listing of the resource.
     *
     * @param  SearchRateRequest  $request
     * @param  RateSearch  $search
     * @return RateCollection
     */
    public function index(SearchRateRequest $request, RateSearch $search): RateCollection
    {
        $rates = $search->getQuery(Rate::query(), $request->validated());
        return new RateCollection($rates->paginate($request->per_page ?? 10));
    }

    /**
     * Store a newly created rate (from_currency_id=incoming_currency_id, to_currency_id=basic_currency_id)
     * and matching pair (with the same cashbox and to_currency_id=incoming_currency_id, from_currency_id=basic_currency_id)
     * collected by incoming filters of currencies
     *
     * @param  StoreRateRequest  $request
     * @return Response
     */
    public function store(StoreRateRequest $request): Response
    {
        $rates = $this->rateService->bulkStore($request->validated());
        return (new RateCollection($rates))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified rate and matching pair with the same cashbox and
     * to_currency_id=from_currency_id, from_currency_id=to_currency_id
     *
     * @param  Rate  $rate
     * @return RateCollection
     */
    public function show(Rate $rate): RateCollection
    {
        $matchingPairRate = Rate::firstOrCreate(
            [
                'to_currency_id' => $rate->from_currency_id,
                'from_currency_id' => $rate->to_currency_id,
                'cashbox_id' => $rate->cashbox_id
            ],
            ['amount' => 0]
        );
        return new RateCollection(collect([$rate, $matchingPairRate]));
    }

    /**
     * Update the specified rate/rates for cashbox/cashboxes
     * collected by incoming filters of cashboxes OR branches OR cities OR regions OR countries
     *
     * @param  UpdateRateRequest  $request
     * @param  Rate  $rate
     * @return Response
     */
    public function update(UpdateRateRequest $request, Rate $rate): Response
    {
        $fields = $request->validated();
        $fields['from_currency_id'] = $rate->from_currency_id;
        $fields['to_currency_id'] = $rate->to_currency_id;
        $fields['cashboxes'][] = $rate->cashbox_id;
        $updatedRatesQuantity = $this->rateService->bulkUpdate($fields);
        return response($updatedRatesQuantity)->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified rate and matching pair with the same cashbox and
     * to_currency_id=from_currency_id, from_currency_id=to_currency_id
     *
     * @param  Rate  $rate
     * @return JsonResponse
     */
    public function destroy(Rate $rate): JsonResponse
    {
        if ($matchingPairRate = Rate::where('to_currency_id', $rate->from_currency_id)
            ->where('from_currency_id', $rate->to_currency_id)
            ->where('cashbox_id', $rate->cashbox_id)
            ->first()) {
            parent::delete($matchingPairRate);
        }
        return parent::delete($rate);
    }

    /**
     * Remove resources from storage by array of ID's.
     *
     * @param  BulkDestroyRateRequest  $request
     * @return JsonResponse
     */
    public function bulkDestroy(BulkDestroyRateRequest $request): JsonResponse
    {
        return parent::bulkDelete(Rate::query(), $request->validated());
    }
}
