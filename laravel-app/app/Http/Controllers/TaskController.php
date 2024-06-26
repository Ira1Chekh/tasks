<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\DTO\TaskDTO;
use App\DTO\TaskListDTO;
use App\Http\Requests\TaskCreateRequest;
use App\Http\Requests\TaskListRequest;
use App\Enums\TaskStatus;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Task Management API",
 *      description="API Documentation for Task Management"
 * )
 * 
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="API Server"
 * )
 * 
 * @OA\SecurityScheme(
 *      type="http",
 *      securityScheme="bearerAuth",
 *      scheme="bearer",
 *      bearerFormat="JWT",
 * )
 */
class TaskController extends Controller
{
    private TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
        $this->authorizeResource(Task::class, 'task');
    }

    /**
     * @OA\Get(
     *      path="/api/tasks",
     *      operationId="searchTasks",
     *      tags={"Tasks"},
     *      summary="Search tasks by description",
     *      description="Returns list of tasks matching the search query",
     *      @OA\Parameter(
     *          name="search",
     *          description="Search query",
     *          in="query",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="status",
     *          description="Search query",
     *          in="query",
     *          @OA\Schema(type="string", enum={"todo", "done"})
     *      ),
     *      @OA\Parameter(
     *          name="priority",
     *          description="Search query",
     *          in="query",
     *          @OA\Schema(type="integer", enum={1,2,3,4,5})
     *      ),
     *       @OA\Parameter(
     *          name="sort",
     *          description="Sort query",
     *          in="query",
     *          @OA\Schema(type="string"),
     *          example="priority asc, created_at desc"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Task"))
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=401, description="Unauthenticated"),
     *      @OA\Response(response=403, description="Forbidden"),
     *      security={{"bearerAuth":{}}}
     * )
     */ 
    public function index(TaskListRequest $request): JsonResponse
    {
        $dto = new TaskListDTO($request);
        $tasks = $this->taskService->listTask($dto);

        return response()->json($tasks);
    }

    /**
     * @OA\Post(
     *      path="/api/tasks",
     *      operationId="storeTask",
     *      tags={"Tasks"},
     *      summary="Store new task",
     *      description="Returns task data",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *             required={"title", "priority"},
     *             @OA\Property(property="title", type="string", example="Task title"),
     *             @OA\Property(property="description", type="string", example="Task description"),
     *             @OA\Property(property="priority", type="integer", example=1),
     *             @OA\Property(property="parent_id", type="integer", example=null),
     *         ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Task")
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=401, description="Unauthenticated"),
     *      @OA\Response(response=403, description="Forbidden"),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function store(TaskCreateRequest $request): JsonResponse
    {
        $dto = new TaskDTO(
            id: null,
            title: $request->get('title'),
            description: $request->get('description'),
            status: TaskStatus::TODO,
            priority: $request->get('priority'),
            user_id: $request->user()->id,
            completed_at: $request->get('completed_at'),
            parent_id: $request->get('parent_id')
        );
        $task = $this->taskService->createTask($dto);

        return response()->json($task, 201);
    }

    /**
     * @OA\Get(
     *      path="/api/tasks/{id}",
     *      operationId="getTaskById",
     *      tags={"Tasks"},
     *      summary="Get task information",
     *      description="Returns task data",
     *      @OA\Parameter(
     *          name="id",
     *          description="Task id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Task")
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=401, description="Unauthenticated"),
     *      @OA\Response(response=403, description="Forbidden"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function show(Task $task): JsonResponse
    {
        $task = $this->taskService->showTask($task);

        return response()->json($task);
    }

    /**
     * @OA\Put(
     *      path="/api/tasks/{id}",
     *      operationId="updateTask",
     *      tags={"Tasks"},
     *      summary="Update existing task",
     *      description="Returns updated task data",
     *      @OA\Parameter(
     *          name="id",
     *          description="Task id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *             required={"title", "priority"},
     *             @OA\Property(property="title", type="string", example="Task title"),
     *             @OA\Property(property="description", type="string", example="Task description"),
     *             @OA\Property(property="priority", type="integer", example=1),
     *             @OA\Property(property="parent_id", type="integer", example=null),
     *         ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Task")
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=401, description="Unauthenticated"),
     *      @OA\Response(response=403, description="Forbidden"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function update(TaskCreateRequest $request, Task $task): JsonResponse
    {
        $dto = new TaskDTO(
            id: $task->id,
            title: $request->get('title'),
            description: $request->get('description'),
            status: $task->status,
            priority: $request->get('priority'),
            user_id: $request->user()->id,
            completed_at: $request->get('completed_at'),
            parent_id: $request->get('parent_id')
        );
        $task = $this->taskService->updateTask($dto, $task);

        return response()->json($task, 200);
    }

    /**
     * @OA\Patch(
     *      path="/api/tasks/{id}/complete",
     *      operationId="completeTask",
     *      tags={"Tasks"},
     *      summary="Complete existing task",
     *      description="Returns updated task data",
     *      @OA\Parameter(
     *          name="id",
     *          description="Task id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Task")
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=401, description="Unauthenticated"),
     *      @OA\Response(response=403, description="Forbidden"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function complete(Task $task): JsonResponse
    {
        Gate::authorize('complete', $task);
        $dto = new TaskDTO(
            id: $task->id,
            title: $task->title,
            description: $task->description,
            status: TaskStatus::DONE,
            priority: $task->priority,
            user_id: $task->user_id,
            completed_at: date('Y-m-d H:i:s'),
            parent_id: $task->parent_id
        );
        $task = $this->taskService->completeTask($dto, $task);

        return response()->json($task, 200);
    }

   /**
     * @OA\Delete(
     *      path="/api/tasks/{id}",
     *      operationId="deleteTask",
     *      tags={"Tasks"},
     *      summary="Delete existing task",
     *      description="Deletes a task and returns no content",
     *      @OA\Parameter(
     *          name="id",
     *          description="Task id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=401, description="Unauthenticated"),
     *      @OA\Response(response=403, description="Forbidden"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function destroy(Task $task): JsonResponse
    {
        $task->delete();
        return response()->json(null, 204);
    }
}
