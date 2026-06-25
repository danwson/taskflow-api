<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Task $task): JsonResponse
    {
        $this->authorize('viewAny', [Comment::class, $task]);

        return response()->json($task->comments()->with('author')->get());
    }

    public function store(Request $request, Task $task): JsonResponse
    {
        $this->authorize('create', [Comment::class, $task]);

        $validated = $request->validate([
            'body' => 'required|string',
        ]);

        $comment = $task->comments()->create([
            'body'    => $validated['body'],
            'user_id' => $request->user()->id,
        ]);

        return response()->json($comment->load('author'), 201);
    }

    public function update(Request $request, Comment $comment): JsonResponse
    {
        $this->authorize('update', $comment);

        $validated = $request->validate([
            'body' => 'required|string',
        ]);

        $comment->update($validated);

        return response()->json($comment->load('author'));
    }

    public function destroy(Comment $comment): JsonResponse
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json(null, 204);
    }
}
