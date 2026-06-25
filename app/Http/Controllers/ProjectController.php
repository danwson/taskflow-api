<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Workspace;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Workspace $workspace): JsonResponse
    {
        $this->authorize('viewAny', [Project::class, $workspace]);

        return response()->json($workspace->projects);
    }

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

    public function show(Workspace $workspace, Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        return response()->json($project);
    }

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

    public function destroy(Workspace $workspace, Project $project): JsonResponse
    {
        $this->authorize('delete', $project);

        $project->delete();

        return response()->json(null, 204);
    }
}
