<?php

namespace App\Http\Controllers;

use App\Models\Webhook;
use App\Models\Workspace;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class WebhookController extends Controller
{
    #[OA\Get(path: '/api/workspaces/{workspace}/webhooks', summary: 'Listar webhooks', security: [['bearerAuth' => []]], tags: ['Webhooks'], parameters: [new OA\Parameter(name: 'workspace', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Lista de webhooks')])]
    public function index(Workspace $workspace): JsonResponse
    {
        $this->authorize('viewAny', [Webhook::class, $workspace]);

        return response()->json($workspace->webhooks);
    }

    #[OA\Post(
        path: '/api/workspaces/{workspace}/webhooks',
        summary: 'Criar webhook',
        security: [['bearerAuth' => []]],
        tags: ['Webhooks'],
        parameters: [new OA\Parameter(name: 'workspace', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
            required: ['url', 'events'],
            properties: [
                new OA\Property(property: 'url', type: 'string', example: 'https://webhook.site/xxx'),
                new OA\Property(property: 'events', type: 'array', items: new OA\Items(type: 'string', enum: ['task.created', 'task.completed', 'task.overdue', 'comment.created'])),
                new OA\Property(property: 'active', type: 'boolean'),
            ]
        )),
        responses: [new OA\Response(response: 201, description: 'Webhook criado')]
    )]
    public function store(Request $request, Workspace $workspace): JsonResponse
    {
        $this->authorize('create', [Webhook::class, $workspace]);

        $validated = $request->validate([
            'url'      => 'required|url',
            'events'   => 'required|array|min:1',
            'events.*' => 'in:task.created,task.completed,task.overdue,comment.created',
            'active'   => 'boolean',
        ]);

        $webhook = $workspace->webhooks()->create($validated);

        return response()->json($webhook, 201);
    }

    #[OA\Get(path: '/api/webhooks/{webhook}', summary: 'Ver webhook com deliveries', security: [['bearerAuth' => []]], tags: ['Webhooks'], parameters: [new OA\Parameter(name: 'webhook', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Detalhes com histórico')])]
    public function show(Workspace $workspace, Webhook $webhook): JsonResponse
    {
        $this->authorize('view', $webhook);

        return response()->json($webhook->load('deliveries'));
    }

    #[OA\Put(
        path: '/api/webhooks/{webhook}',
        summary: 'Atualizar webhook',
        security: [['bearerAuth' => []]],
        tags: ['Webhooks'],
        parameters: [new OA\Parameter(name: 'webhook', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
            required: ['url', 'events'],
            properties: [
                new OA\Property(property: 'url', type: 'string'),
                new OA\Property(property: 'events', type: 'array', items: new OA\Items(type: 'string')),
                new OA\Property(property: 'active', type: 'boolean'),
            ]
        )),
        responses: [new OA\Response(response: 200, description: 'Webhook atualizado')]
    )]
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

    #[OA\Delete(path: '/api/webhooks/{webhook}', summary: 'Deletar webhook', security: [['bearerAuth' => []]], tags: ['Webhooks'], parameters: [new OA\Parameter(name: 'webhook', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 204, description: 'Deletado')])]
    public function destroy(Workspace $workspace, Webhook $webhook): JsonResponse
    {
        $this->authorize('delete', $webhook);

        $webhook->delete();

        return response()->json(null, 204);
    }
}
