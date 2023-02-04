<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\{
    SearchRequest,
    StoreCashboxRequest,
    UpdateCashboxRequest,
    BulkDestroyCashboxRequest
};
use App\Services\Search\CashboxSearch;
use App\Http\Resources\{CashboxCollection, CashboxResource};
use App\Models\Cashbox;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CashboxController extends BaseCollectionController
{
    /**
     * Display a listing of the resource.
     *
     * @param  SearchRequest  $request
     * @param  CashboxSearch  $search
     * @return CashboxCollection
     */
    public function index(SearchRequest $request, CashboxSearch $search): CashboxCollection
    {
        $cashboxes = $search->getQuery(Cashbox::query(), $request->validated());
        return new CashboxCollection($cashboxes->paginate($request->per_page ?? 10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreCashboxRequest  $request
     * @return CashboxResource
     */
    public function store(StoreCashboxRequest $request): CashboxResource
    {
        $cashbox = Cashbox::create($request->validated());
        return new CashboxResource($cashbox);
    }

    /**
     * Display the specified resource.
     *
     * @param  Cashbox  $cashbox
     * @return CashboxResource
     */
    public function show(Cashbox $cashbox): CashboxResource
    {
        return new CashboxResource($cashbox);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateCashboxRequest  $request
     * @param  Cashbox  $cashbox
     * @return JsonResponse
     */
    public function update(UpdateCashboxRequest $request, Cashbox $cashbox): JsonResponse
    {
        $cashbox->update($request->safe()->only(['name', 'branch_id', 'settings']));
        return (new CashboxResource($cashbox))->response()->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Cashbox  $cashbox
     * @return JsonResponse
     */
    public function destroy(Cashbox $cashbox): JsonResponse
    {
        return parent::delete($cashbox);
    }

    /**
     * Remove resources from storage by array of ID's.
     *
     * @param  BulkDestroyCashboxRequest  $request
     * @return JsonResponse
     */
    public function bulkDestroy(BulkDestroyCashboxRequest $request): JsonResponse
    {
        return parent::bulkDelete(Cashbox::query(), $request->validated());
    }
}
