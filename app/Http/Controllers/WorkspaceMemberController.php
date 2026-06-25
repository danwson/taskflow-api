<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class WorkspaceMemberController extends Controller
{
    #[OA\Post(
        path: '/api/workspaces/{workspace}/members',
        summary: 'Convidar membro',
        security: [['bearerAuth' => []]],
        tags: ['Members'],
        parameters: [new OA\Parameter(name: 'workspace', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(required: ['email'], properties: [new OA\Property(property: 'email', type: 'string', example: 'joao@test.com')])),
        responses: [new OA\Response(response: 201, description: 'Membro adicionado'), new OA\Response(response: 422, description: 'Já é membro')]
    )]
    public function store(Request $request, Workspace $workspace): JsonResponse
    {
        $this->authorize('manageMembers', $workspace);

        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if ($workspace->members()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'User is already a member.'], 422);
        }

        $workspace->members()->attach($user->id, ['role' => 'member']);

        return response()->json(['message' => 'Member added successfully.'], 201);
    }

    #[OA\Delete(
        path: '/api/workspaces/{workspace}/members/{user}',
        summary: 'Remover membro',
        security: [['bearerAuth' => []]],
        tags: ['Members'],
        parameters: [
            new OA\Parameter(name: 'workspace', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'user', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [new OA\Response(response: 204, description: 'Removido')]
    )]
    public function destroy(Workspace $workspace, User $user): JsonResponse
    {
        $this->authorize('manageMembers', $workspace);

        if ($workspace->owner_id === $user->id) {
            return response()->json(['message' => 'Cannot remove the workspace owner.'], 422);
        }

        $workspace->members()->detach($user->id);

        return response()->json(null, 204);
    }
}
