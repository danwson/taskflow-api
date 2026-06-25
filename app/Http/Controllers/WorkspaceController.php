<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Workspace::class);

        $workspaces = $request->user()->workspaces()->with('owner')->get();

        return response()->json($workspaces);
    }

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

    public function show(Workspace $workspace): JsonResponse
    {
        $this->authorize('view', $workspace);

        return response()->json($workspace->load('owner', 'members'));
    }

    public function update(Request $request, Workspace $workspace): JsonResponse
    {
        $this->authorize('update', $workspace);

        $validated = $request->validate(['name' => 'required|string|max:255']);

        $workspace->update($validated);

        return response()->json($workspace);
    }

    public function destroy(Workspace $workspace): JsonResponse
    {
        $this->authorize('delete', $workspace);

        $workspace->delete();

        return response()->json(null, 204);
    }
}
