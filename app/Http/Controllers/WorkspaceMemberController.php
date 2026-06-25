<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkspaceMemberController extends Controller
{
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
