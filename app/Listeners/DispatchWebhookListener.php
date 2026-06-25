<?php

namespace App\Listeners;

use App\Events\CommentCreated;
use App\Events\TaskCompleted;
use App\Events\TaskCreated;
use App\Events\TaskOverdue;
use App\Jobs\SendWebhookRequestJob;
use App\Models\Webhook;

class DispatchWebhookListener
{
    public function handle(object $event): void
    {
        [$eventName, $payload, $workspaceId] = match (true) {
            $event instanceof TaskCreated   => [
                'task.created',
                ['task' => $event->task->load('project')->toArray()],
                $event->task->project->workspace_id,
            ],
            $event instanceof TaskCompleted => [
                'task.completed',
                ['task' => $event->task->load('project')->toArray()],
                $event->task->project->workspace_id,
            ],
            $event instanceof TaskOverdue   => [
                'task.overdue',
                ['task' => $event->task->load('project')->toArray()],
                $event->task->project->workspace_id,
            ],
            $event instanceof CommentCreated => [
                'comment.created',
                ['comment' => $event->comment->load('task')->toArray()],
                $event->comment->task->project->workspace_id,
            ],
            default => [null, [], null],
        };

        if (!$eventName) {
            return;
        }

        Webhook::where('workspace_id', $workspaceId)
            ->where('active', true)
            ->whereJsonContains('events', $eventName)
            ->each(fn (Webhook $webhook) => SendWebhookRequestJob::dispatch($webhook, $eventName, $payload));
    }
}
