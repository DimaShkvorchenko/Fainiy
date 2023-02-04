<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SearchWalletRequest;
use App\Services\Search\WalletSearch;
use App\Http\Resources\{WalletCollection, WalletResource};
use App\Models\Wallet;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class WalletController extends BaseCollectionController
{
    /**
     * Display a listing of the resource.
     *
     * @param  SearchWalletRequest  $request
     * @param  WalletSearch  $search
     * @return WalletCollection
     */
    public function index(SearchWalletRequest $request, WalletSearch $search): WalletCollection
    {
        $wallet = $search->getQuery(Wallet::query(), $request->validated());
        return new WalletCollection($wallet->paginate($request->per_page ?? 10));
    }

    /**
     * Display the specified resource.
     *
     * @param  Wallet  $wallet
     * @return WalletResource
     */
    public function show(Wallet $wallet): WalletResource
    {
        return new WalletResource($wallet);
    }
}
