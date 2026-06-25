<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'         => $this->faker->words(3, true),
            'description'  => $this->faker->sentence(),
            'workspace_id' => Workspace::factory(),
        ];
    }
}
