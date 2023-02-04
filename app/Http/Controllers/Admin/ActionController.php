<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SearchActionRequest;
use App\Services\Search\ActionSearch;
use App\Http\Resources\{ActionCollection, ActionResource};
use App\Models\Action;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ActionController extends BaseCollectionController
{
    /**
     * Display a listing of the resource.
     *
     * @param  SearchActionRequest  $request
     * @param  ActionSearch  $search
     * @return ActionCollection
     */
    public function index(SearchActionRequest $request, ActionSearch $search): ActionCollection
    {
        $actions = $search->getQuery(Action::query(), $request->validated());
        return new ActionCollection($actions->paginate($request->per_page ?? 10), $actions->sum('amount'));
    }

    /**
     * Display the specified resource.
     *
     * @param  Action  $action
     * @return ActionResource
     */
    public function show(Action $action): ActionResource
    {
        return new ActionResource($action);
    }
}
