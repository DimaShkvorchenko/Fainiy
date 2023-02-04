<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\{
    SearchRequest,
    StoreSettingRequest,
    UpdateSettingRequest
};
use App\Services\Search\SettingSearch;
use App\Http\Resources\{SettingCollection, SettingResource};
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class SettingController extends BaseCollectionController
{
    /**
     * Display a listing of the resource.
     *
     * @param  SearchRequest  $request
     * @param  SettingSearch  $search
     * @return SettingCollection
     */
    public function index(SearchRequest $request, SettingSearch $search): SettingCollection
    {
        $settings = $search->getQuery(Setting::query(), $request->validated());
        return new SettingCollection($settings->paginate($request->per_page ?? 10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreSettingRequest  $request
     * @return SettingResource
     */
    public function store(StoreSettingRequest $request): SettingResource
    {
        $setting = Setting::create($request->validated());
        return new SettingResource($setting);
    }

    /**
     * Display the specified resource.
     *
     * @param  Setting  $setting
     * @return SettingResource
     */
    public function show(Setting $setting): SettingResource
    {
        return new SettingResource($setting);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateSettingRequest  $request
     * @param  Setting  $setting
     * @return JsonResponse
     */
    public function update(UpdateSettingRequest $request, Setting $setting): JsonResponse
    {
        $setting->update($request->safe()->only(['value']));
        return (new SettingResource($setting))->response()->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Setting  $setting
     * @return JsonResponse|Response
     */
    public function destroy(Setting $setting): JsonResponse|Response
    {
        if (in_array($setting->code, ['basic_currency', 'income_commission'])) {
            abort(response(['message' => $setting->code . ' setting can not be deleted'], Response::HTTP_UNPROCESSABLE_ENTITY));
        }
        return parent::delete($setting);
    }
}
