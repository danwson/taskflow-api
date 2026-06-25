<?php

namespace App\Providers;

use App\Events\CommentCreated;
use App\Events\TaskCompleted;
use App\Events\TaskCreated;
use App\Events\TaskOverdue;
use App\Listeners\DispatchWebhookListener;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Event::listen(TaskCreated::class, DispatchWebhookListener::class);
        Event::listen(TaskCompleted::class, DispatchWebhookListener::class);
        Event::listen(TaskOverdue::class, DispatchWebhookListener::class);
        Event::listen(CommentCreated::class, DispatchWebhookListener::class);
    }
}
