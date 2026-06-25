<?php

namespace App\Http\Controllers;

use App\Events\CommentCreated;
use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CommentController extends Controller
{
    #[OA\Get(path: '/api/tasks/{task}/comments', summary: 'Listar comentários', security: [['bearerAuth' => []]], tags: ['Comments'], parameters: [new OA\Parameter(name: 'task', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Lista de comentários')])]
    public function index(Task $task): JsonResponse
    {
        $this->authorize('viewAny', [Comment::class, $task]);

        return response()->json($task->comments()->with('author')->get());
    }

    #[OA\Post(
        path: '/api/tasks/{task}/comments',
        summary: 'Criar comentário',
        security: [['bearerAuth' => []]],
        tags: ['Comments'],
        parameters: [new OA\Parameter(name: 'task', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(required: ['body'], properties: [new OA\Property(property: 'body', type: 'string')])),
        responses: [new OA\Response(response: 201, description: 'Comentário criado')]
    )]
    public function store(Request $request, Task $task): JsonResponse
    {
        $this->authorize('create', [Comment::class, $task]);

        $validated = $request->validate(['body' => 'required|string']);

        $comment = $task->comments()->create([
            'body'    => $validated['body'],
            'user_id' => $request->user()->id,
        ]);

        CommentCreated::dispatch($comment);

        return response()->json($comment->load('author'), 201);
    }

    #[OA\Put(
        path: '/api/comments/{comment}',
        summary: 'Editar comentário',
        security: [['bearerAuth' => []]],
        tags: ['Comments'],
        parameters: [new OA\Parameter(name: 'comment', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(required: ['body'], properties: [new OA\Property(property: 'body', type: 'string')])),
        responses: [new OA\Response(response: 200, description: 'Comentário atualizado')]
    )]
    public function update(Request $request, Comment $comment): JsonResponse
    {
        $this->authorize('update', $comment);

        $validated = $request->validate(['body' => 'required|string']);

        $comment->update($validated);

        return response()->json($comment->load('author'));
    }

    #[OA\Delete(path: '/api/comments/{comment}', summary: 'Deletar comentário', security: [['bearerAuth' => []]], tags: ['Comments'], parameters: [new OA\Parameter(name: 'comment', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 204, description: 'Deletado')])]
    public function destroy(Comment $comment): JsonResponse
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json(null, 204);
    }
}
