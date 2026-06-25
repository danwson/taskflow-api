<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use App\Models\Workspace;

class ProjectPolicy
{
    public function viewAny(User $user, Workspace $workspace): bool
    {
        return $workspace->members()->where('user_id', $user->id)->exists()
            || $workspace->owner_id === $user->id;
    }

    public function view(User $user, Project $project): bool
    {
        $workspace = $project->workspace;

        return $workspace->members()->where('user_id', $user->id)->exists()
            || $workspace->owner_id === $user->id;
    }

    public function create(User $user, Workspace $workspace): bool
    {
        return $workspace->members()->where('user_id', $user->id)->exists()
            || $workspace->owner_id === $user->id;
    }

    public function update(User $user, Project $project): bool
    {
        $workspace = $project->workspace;

        return $workspace->members()->where('user_id', $user->id)->exists()
            || $workspace->owner_id === $user->id;
    }

    public function delete(User $user, Project $project): bool
    {
        return $project->workspace->owner_id === $user->id;
    }
}
