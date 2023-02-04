<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\{Model, Builder};
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Remove the specified resource from storage.
     *
     * @param Model $model
     * @return JsonResponse
     */
    protected function delete(Model $model): JsonResponse
    {
        return response()->json($model->delete(), Response::HTTP_NO_CONTENT);
    }

    /**
     * Remove resources from storage by array of ID's.
     *
     * @param Builder $query
     * @param array $fields
     * @return JsonResponse
     */
    protected function bulkDelete(Builder $query, array $fields): JsonResponse
    {
        return response()->json($query->whereIn('id', $fields['ids'])->delete(), Response::HTTP_NO_CONTENT);
    }
}
