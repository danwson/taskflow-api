<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;

class TaskPolicy
{
    private function isMember(User $user, Workspace $workspace): bool
    {
        return $workspace->owner_id === $user->id
            || $workspace->members()->where('user_id', $user->id)->exists();
    }

    public function viewAny(User $user, Project $project): bool
    {
        return $this->isMember($user, $project->workspace);
    }

    public function view(User $user, Task $task): bool
    {
        return $this->isMember($user, $task->project->workspace);
    }

    public function create(User $user, Project $project): bool
    {
        return $this->isMember($user, $project->workspace);
    }

    public function update(User $user, Task $task): bool
    {
        return $this->isMember($user, $task->project->workspace);
    }

    public function delete(User $user, Task $task): bool
    {
        return $task->project->workspace->owner_id === $user->id;
    }
}
