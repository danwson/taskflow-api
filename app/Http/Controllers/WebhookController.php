<?php

namespace App\Http\Controllers;

use App\Models\Webhook;
use App\Models\Workspace;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function index(Workspace $workspace): JsonResponse
    {
        $this->authorize('viewAny', [Webhook::class, $workspace]);

        return response()->json($workspace->webhooks);
    }

    public function store(Request $request, Workspace $workspace): JsonResponse
    {
        $this->authorize('create', [Webhook::class, $workspace]);

        $validated = $request->validate([
            'url'    => 'required|url',
            'events' => 'required|array|min:1',
            'events.*' => 'in:task.created,task.completed,task.overdue,comment.created',
            'active' => 'boolean',
        ]);

        $webhook = $workspace->webhooks()->create($validated);

        return response()->json($webhook, 201);
    }

    public function show(Workspace $workspace, Webhook $webhook): JsonResponse
    {
        $this->authorize('view', $webhook);

        return response()->json($webhook->load('deliveries'));
    }

    public function update(Request $request, Workspace $workspace, Webhook $webhook): JsonResponse
    {
        $this->authorize('update', $webhook);

        $validated = $request->validate([
            'url'      => 'required|url',
            'events'   => 'required|array|min:1',
            'events.*' => 'in:task.created,task.completed,task.overdue,comment.created',
            'active'   => 'boolean',
        ]);

        $webhook->update($validated);

        return response()->json($webhook);
    }

    public function destroy(Workspace $workspace, Webhook $webhook): JsonResponse
    {
        $this->authorize('delete', $webhook);

        $webhook->delete();

        return response()->json(null, 204);
    }
}
