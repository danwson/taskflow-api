<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\Task;
use App\Models\User;

class CommentPolicy
{
    private function isMember(User $user, Task $task): bool
    {
        $workspace = $task->project->workspace;

        return $workspace->owner_id === $user->id
            || $workspace->members()->where('user_id', $user->id)->exists();
    }

    public function viewAny(User $user, Task $task): bool
    {
        return $this->isMember($user, $task);
    }

    public function create(User $user, Task $task): bool
    {
        return $this->isMember($user, $task);
    }

    public function update(User $user, Comment $comment): bool
    {
        return $comment->user_id === $user->id;
    }

    public function delete(User $user, Comment $comment): bool
    {
        $workspace = $comment->task->project->workspace;

        return $comment->user_id === $user->id
            || $workspace->owner_id === $user->id;
    }
}
