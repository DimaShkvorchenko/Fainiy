<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\{
    SearchTransferRequest,
    StoreTransferRequest,
    UpdateTransferRequest
};
use App\Services\Search\TransferSearch;
use App\Http\Resources\{TransferCollection, TransferResource};
use App\Models\Transfer;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TransferController extends BaseCollectionController
{
    /**
     * Display a listing of the resource.
     *
     * @param  SearchTransferRequest  $request
     * @param  TransferSearch  $search
     * @return TransferCollection
     */
    public function index(SearchTransferRequest $request, TransferSearch $search): TransferCollection
    {
        $transfers = $search->getQuery(Transfer::query(), $request->validated());
        return new TransferCollection($transfers->paginate($request->per_page ?? 10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreTransferRequest  $request
     * @return TransferResource
     */
    public function store(StoreTransferRequest $request): TransferResource
    {
        $transfer = Transfer::create($request->validated());
        $this->actionService->log($transfer, 'store');
        return new TransferResource($transfer);
    }

    /**
     * Display the specified resource.
     *
     * @param  Transfer  $transfer
     * @return TransferResource
     */
    public function show(Transfer $transfer): TransferResource
    {
        return new TransferResource($transfer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateTransferRequest  $request
     * @param  Transfer  $transfer
     * @return JsonResponse
     */
    public function update(UpdateTransferRequest $request, Transfer $transfer): JsonResponse
    {
        $transfer->update($request->safe()->only(['access_code', 'amount', 'to_cashbox_id', 'description', 'status', 'time_of_issue']));
        $this->actionService->log($transfer, 'update');
        return (new TransferResource($transfer))->response()->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * Set status done for the specified resource in storage.
     *
     * @param  Transfer  $transfer
     * @return JsonResponse
     */
    public function setStatusDone(Transfer $transfer): JsonResponse
    {
        $transfer->update(['status' => 'done']);
        return (new TransferResource($transfer))->response()->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * Set status cancelled for the specified resource in storage.
     *
     * @param  Transfer  $transfer
     * @return Response|JsonResponse
     */
    public function setStatusCancelled(Transfer $transfer): Response|JsonResponse
    {
        if ($transfer->status == 'done') {
            return response('Transfer already has status done', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        if ($transfer->status == 'cancelled') {
            return response('Transfer already has status cancelled', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $transfer->update(['status' => 'cancelled']);
        return (new TransferResource($transfer))->response()->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Transfer  $transfer
     * @return JsonResponse
     */
    public function destroy(Transfer $transfer): JsonResponse
    {
        if ($result = parent::delete($transfer)) {
            $this->actionService->log($transfer, 'destroy');
        }
        return $result;
    }
}
