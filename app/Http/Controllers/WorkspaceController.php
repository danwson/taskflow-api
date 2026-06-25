<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class WorkspaceController extends Controller
{
    #[OA\Get(path: '/api/workspaces', summary: 'Listar workspaces do usuário', security: [['bearerAuth' => []]], tags: ['Workspaces'], responses: [new OA\Response(response: 200, description: 'Lista de workspaces')])]
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Workspace::class);

        $workspaces = $request->user()->workspaces()->with('owner')->get();

        return response()->json($workspaces);
    }

    #[OA\Post(
        path: '/api/workspaces',
        summary: 'Criar workspace',
        security: [['bearerAuth' => []]],
        tags: ['Workspaces'],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(required: ['name'], properties: [new OA\Property(property: 'name', type: 'string', example: 'Meu Workspace')])),
        responses: [new OA\Response(response: 201, description: 'Workspace criado')]
    )]
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Workspace::class);

        $validated = $request->validate(['name' => 'required|string|max:255']);

        $workspace = Workspace::create([
            'name'     => $validated['name'],
            'owner_id' => $request->user()->id,
        ]);

        $workspace->members()->attach($request->user()->id, ['role' => 'owner']);

        return response()->json($workspace->load('owner'), 201);
    }

    #[OA\Get(path: '/api/workspaces/{workspace}', summary: 'Ver workspace', security: [['bearerAuth' => []]], tags: ['Workspaces'], parameters: [new OA\Parameter(name: 'workspace', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Detalhes do workspace')])]
    public function show(Workspace $workspace): JsonResponse
    {
        $this->authorize('view', $workspace);

        return response()->json($workspace->load('owner', 'members'));
    }

    #[OA\Put(
        path: '/api/workspaces/{workspace}',
        summary: 'Atualizar workspace',
        security: [['bearerAuth' => []]],
        tags: ['Workspaces'],
        parameters: [new OA\Parameter(name: 'workspace', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(required: ['name'], properties: [new OA\Property(property: 'name', type: 'string')])),
        responses: [new OA\Response(response: 200, description: 'Workspace atualizado')]
    )]
    public function update(Request $request, Workspace $workspace): JsonResponse
    {
        $this->authorize('update', $workspace);

        $validated = $request->validate(['name' => 'required|string|max:255']);

        $workspace->update($validated);

        return response()->json($workspace);
    }

    #[OA\Delete(path: '/api/workspaces/{workspace}', summary: 'Deletar workspace', security: [['bearerAuth' => []]], tags: ['Workspaces'], parameters: [new OA\Parameter(name: 'workspace', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 204, description: 'Deletado')])]
    public function destroy(Workspace $workspace): JsonResponse
    {
        $this->authorize('delete', $workspace);

        $workspace->delete();

        return response()->json(null, 204);
    }
}
