<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index(): JsonResponse
    {
        try {
            $tasks = $this->taskService->getAllTasks();
            return response()->json(TaskResource::collection($tasks));
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(TaskRequest $request): JsonResponse
    {
        try {
            $task = $this->taskService->createTask($request->validated());
            return response()->json(new TaskResource($task), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $task = $this->taskService->getTaskById($id);
            return response()->json(new TaskResource($task));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Task not found'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(TaskRequest $request, $id): JsonResponse
    {
        try {
            $task = $this->taskService->updateTask($id, $request->validated());
            return response()->json(new TaskResource($task));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Task not found'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->taskService->deleteTask($id);
            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Task not found'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    protected function handleException(\Exception $e): JsonResponse
    {
        return response()->json(
            [
                'error' => 'An unexpected error occurred. Please try again later.'
            ],
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
