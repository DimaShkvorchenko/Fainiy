<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\{
    SearchRequest,
    StoreTaskRequest,
    UpdateTaskRequest,
    BulkDestroyTaskRequest
};
use App\Services\Search\TaskSearch;
use App\Http\Resources\{TaskCollection, TaskResource};
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends BaseCollectionController
{
    /**
     * Display a listing of the resource.
     *
     * @param  SearchRequest  $request
     * @param  TaskSearch  $search
     * @return TaskCollection
     */
    public function index(SearchRequest $request, TaskSearch $search): TaskCollection
    {
        $tasks = $search->getQuery(Task::query(), $request->validated());
        return new TaskCollection($tasks->paginate($request->per_page ?? 10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreTaskRequest  $request
     * @return TaskResource
     */
    public function store(StoreTaskRequest $request): TaskResource
    {
        $task = Task::create($request->validated());
        return new TaskResource($task);
    }

    /**
     * Display the specified resource.
     *
     * @param  Task  $task
     * @return TaskResource
     */
    public function show(Task $task): TaskResource
    {
        return new TaskResource($task);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateTaskRequest  $request
     * @param  Task  $task
     * @return JsonResponse
     */
    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $task->update($request->safe()->only([
            'staff_id',
            'title',
            'description',
            'tags',
            'file',
            'completion_date'
        ]));
        return (new TaskResource($task))->response()->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Task  $task
     * @return JsonResponse
     */
    public function destroy(Task $task): JsonResponse
    {
        return parent::delete($task);
    }

    /**
     * Remove resources from storage by array of ID's.
     *
     * @param  BulkDestroyTaskRequest  $request
     * @return JsonResponse
     */
    public function bulkDestroy(BulkDestroyTaskRequest $request): JsonResponse
    {
        return parent::bulkDelete(Task::query(), $request->validated());
    }
}
