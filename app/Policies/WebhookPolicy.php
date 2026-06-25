<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Webhook;
use App\Models\Workspace;

class WebhookPolicy
{
    public function viewAny(User $user, Workspace $workspace): bool
    {
        return $workspace->owner_id === $user->id;
    }

    public function view(User $user, Webhook $webhook): bool
    {
        return $webhook->workspace->owner_id === $user->id;
    }

    public function create(User $user, Workspace $workspace): bool
    {
        return $workspace->owner_id === $user->id;
    }

    public function update(User $user, Webhook $webhook): bool
    {
        return $webhook->workspace->owner_id === $user->id;
    }

    public function delete(User $user, Webhook $webhook): bool
    {
        return $webhook->workspace->owner_id === $user->id;
    }
}
