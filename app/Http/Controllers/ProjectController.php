<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Workspace;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ProjectController extends Controller
{
    #[OA\Get(path: '/api/workspaces/{workspace}/projects', summary: 'Listar projetos', security: [['bearerAuth' => []]], tags: ['Projects'], parameters: [new OA\Parameter(name: 'workspace', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Lista de projetos')])]
    public function index(Workspace $workspace): JsonResponse
    {
        $this->authorize('viewAny', [Project::class, $workspace]);

        return response()->json($workspace->projects);
    }

    #[OA\Post(
        path: '/api/workspaces/{workspace}/projects',
        summary: 'Criar projeto',
        security: [['bearerAuth' => []]],
        tags: ['Projects'],
        parameters: [new OA\Parameter(name: 'workspace', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(required: ['name'], properties: [new OA\Property(property: 'name', type: 'string'), new OA\Property(property: 'description', type: 'string')])),
        responses: [new OA\Response(response: 201, description: 'Projeto criado')]
    )]
    public function store(Request $request, Workspace $workspace): JsonResponse
    {
        $this->authorize('create', [Project::class, $workspace]);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project = $workspace->projects()->create($validated);

        return response()->json($project, 201);
    }

    #[OA\Get(path: '/api/projects/{project}', summary: 'Ver projeto', security: [['bearerAuth' => []]], tags: ['Projects'], parameters: [new OA\Parameter(name: 'project', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Detalhes do projeto')])]
    public function show(Workspace $workspace, Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        return response()->json($project);
    }

    #[OA\Put(
        path: '/api/projects/{project}',
        summary: 'Atualizar projeto',
        security: [['bearerAuth' => []]],
        tags: ['Projects'],
        parameters: [new OA\Parameter(name: 'project', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(required: ['name'], properties: [new OA\Property(property: 'name', type: 'string'), new OA\Property(property: 'description', type: 'string')])),
        responses: [new OA\Response(response: 200, description: 'Projeto atualizado')]
    )]
    public function update(Request $request, Workspace $workspace, Project $project): JsonResponse
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project->update($validated);

        return response()->json($project);
    }

    #[OA\Delete(path: '/api/projects/{project}', summary: 'Deletar projeto', security: [['bearerAuth' => []]], tags: ['Projects'], parameters: [new OA\Parameter(name: 'project', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 204, description: 'Deletado')])]
    public function destroy(Workspace $workspace, Project $project): JsonResponse
    {
        $this->authorize('delete', $project);

        $project->delete();

        return response()->json(null, 204);
    }
}
