<?php

namespace Database\Factories;

use App\Models\Webhook;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Webhook>
 */
class WebhookFactory extends Factory
{
    public function definition(): array
    {
        return [
            'url'          => $this->faker->url(),
            'events'       => ['task.created'],
            'active'       => true,
            'workspace_id' => Workspace::factory(),
        ];
    }
}
