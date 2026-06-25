<?php

namespace App\Http\Controllers;

use App\Events\TaskCompleted;
use App\Events\TaskCreated;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class TaskController extends Controller
{
    #[OA\Get(path: '/api/projects/{project}/tasks', summary: 'Listar tasks', security: [['bearerAuth' => []]], tags: ['Tasks'], parameters: [new OA\Parameter(name: 'project', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Lista de tasks')])]
    public function index(Project $project): JsonResponse
    {
        $this->authorize('viewAny', [Task::class, $project]);

        return response()->json($project->tasks()->with('assignee')->get());
    }

    #[OA\Post(
        path: '/api/projects/{project}/tasks',
        summary: 'Criar task',
        security: [['bearerAuth' => []]],
        tags: ['Tasks'],
        parameters: [new OA\Parameter(name: 'project', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
            required: ['title'],
            properties: [
                new OA\Property(property: 'title', type: 'string'),
                new OA\Property(property: 'description', type: 'string'),
                new OA\Property(property: 'status', type: 'string', enum: ['todo', 'in_progress', 'done']),
                new OA\Property(property: 'priority', type: 'string', enum: ['low', 'medium', 'high']),
                new OA\Property(property: 'due_date', type: 'string', format: 'date'),
                new OA\Property(property: 'assigned_to', type: 'integer'),
            ]
        )),
        responses: [new OA\Response(response: 201, description: 'Task criada')]
    )]
    public function store(Request $request, Project $project): JsonResponse
    {
        $this->authorize('create', [Task::class, $project]);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'in:todo,in_progress,done',
            'priority'    => 'in:low,medium,high',
            'due_date'    => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $task = $project->tasks()->create($validated);

        TaskCreated::dispatch($task);

        return response()->json($task->load('assignee'), 201);
    }

    #[OA\Get(path: '/api/tasks/{task}', summary: 'Ver task', security: [['bearerAuth' => []]], tags: ['Tasks'], parameters: [new OA\Parameter(name: 'task', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Detalhes da task')])]
    public function show(Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        return response()->json($task->load('assignee', 'project'));
    }

    #[OA\Put(
        path: '/api/tasks/{task}',
        summary: 'Atualizar task',
        security: [['bearerAuth' => []]],
        tags: ['Tasks'],
        parameters: [new OA\Parameter(name: 'task', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
            required: ['title'],
            properties: [
                new OA\Property(property: 'title', type: 'string'),
                new OA\Property(property: 'status', type: 'string', enum: ['todo', 'in_progress', 'done']),
                new OA\Property(property: 'priority', type: 'string', enum: ['low', 'medium', 'high']),
                new OA\Property(property: 'due_date', type: 'string', format: 'date'),
                new OA\Property(property: 'assigned_to', type: 'integer'),
            ]
        )),
        responses: [new OA\Response(response: 200, description: 'Task atualizada')]
    )]
    public function update(Request $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'in:todo,in_progress,done',
            'priority'    => 'in:low,medium,high',
            'due_date'    => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $previousStatus = $task->status;
        $task->update($validated);

        if ($previousStatus !== 'done' && $task->status === 'done') {
            TaskCompleted::dispatch($task);
        }

        return response()->json($task->load('assignee'));
    }

    #[OA\Delete(path: '/api/tasks/{task}', summary: 'Deletar task', security: [['bearerAuth' => []]], tags: ['Tasks'], parameters: [new OA\Parameter(name: 'task', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 204, description: 'Deletada')])]
    public function destroy(Task $task): JsonResponse
    {
        $this->authorize('delete', $task);

        $task->delete();

        return response()->json(null, 204);
    }
}
