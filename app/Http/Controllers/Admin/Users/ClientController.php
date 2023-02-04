<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Requests\SearchRequest;
use App\Services\Search\UserSearch;
use App\Http\Requests\User\{StoreClientRequest, UpdateClientRequest, BulkDestroyClientRequest};
use App\Http\Resources\{ClientCollection, ClientResource};
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ClientController extends BaseUserController
{
    /**
     * Display a listing of the resource.
     *
     * @param  SearchRequest  $request
     * @param  UserSearch  $search
     * @return ClientCollection
     */
    public function index(SearchRequest $request, UserSearch $search): ClientCollection
    {
        $clients = $search->getQuery(User::client(), $request->validated());
        return new ClientCollection($clients->paginate($request->per_page ?? 10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreClientRequest $request
     * @return ClientResource
     */
    public function store(StoreClientRequest $request): ClientResource
    {
        $client = $this->service->createClient($request->validated());
        return new ClientResource($client);
    }

    /**
     * Display the specified resource.
     *
     * @param User $client
     * @return ClientResource
     */
    public function show(User $client): ClientResource
    {
        return new ClientResource($client);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateClientRequest $request
     * @param User $client
     * @return JsonResponse
     */
    public function update(UpdateClientRequest $request, User $client): JsonResponse
    {
        parent::validateUserType($client, User::CLIENT_TYPE);
        $client->update($request->safe()->only([
            'first_name',
            'last_name',
            'phone',
            'telegram',
            'code',
            'modules',
            'other_data'
        ]));
        return (new ClientResource($client))->response()->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $client
     * @return JsonResponse
     */
    public function destroy(User $client): JsonResponse
    {
        parent::validateUserType($client, User::CLIENT_TYPE);
        return parent::delete($client);
    }

    /**
     * Remove resources from storage by array of ID's.
     *
     * @param  BulkDestroyClientRequest  $request
     * @return JsonResponse
     */
    public function bulkDestroy(BulkDestroyClientRequest $request): JsonResponse
    {
        return parent::bulkDelete(User::query(), $request->validated());
    }
}
