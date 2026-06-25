<?php

use App\Events\TaskCompleted;
use App\Events\TaskCreated;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

function projectWithMember(): array
{
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
    $workspace->members()->attach($user->id, ['role' => 'owner']);
    $project = Project::factory()->create(['workspace_id' => $workspace->id]);

    return [$user, $workspace, $project];
}

it('member can create a task', function () {
    Event::fake();
    [$user, $workspace, $project] = projectWithMember();

    $response = $this->actingAs($user)->postJson("/api/projects/{$project->id}/tasks", [
        'title'    => 'Nova Task',
        'priority' => 'high',
    ]);

    $response->assertStatus(201)
        ->assertJsonFragment(['title' => 'Nova Task']);

    Event::assertDispatched(TaskCreated::class);
});

it('member can list tasks', function () {
    [$user, $workspace, $project] = projectWithMember();
    Task::factory()->create(['project_id' => $project->id, 'status' => 'todo']);

    $response = $this->actingAs($user)->getJson("/api/projects/{$project->id}/tasks");

    $response->assertStatus(200)->assertJsonCount(1);
});

it('member can view a task', function () {
    [$user, $workspace, $project] = projectWithMember();
    $task = Task::factory()->create(['project_id' => $project->id]);

    $response = $this->actingAs($user)->getJson("/api/tasks/{$task->id}");

    $response->assertStatus(200);
});

it('member can update a task', function () {
    [$user, $workspace, $project] = projectWithMember();
    $task = Task::factory()->create(['project_id' => $project->id, 'status' => 'todo']);

    $response = $this->actingAs($user)->putJson("/api/tasks/{$task->id}", [
        'title'  => 'Task Atualizada',
        'status' => 'in_progress',
    ]);

    $response->assertStatus(200)
        ->assertJsonFragment(['status' => 'in_progress']);
});

it('dispatches TaskCompleted when status changes to done', function () {
    Event::fake();
    [$user, $workspace, $project] = projectWithMember();
    $task = Task::factory()->create(['project_id' => $project->id, 'status' => 'todo']);

    $this->actingAs($user)->putJson("/api/tasks/{$task->id}", [
        'title'  => $task->title,
        'status' => 'done',
    ]);

    Event::assertDispatched(TaskCompleted::class);
});

it('does not dispatch TaskCompleted if already done', function () {
    Event::fake();
    [$user, $workspace, $project] = projectWithMember();
    $task = Task::factory()->create(['project_id' => $project->id, 'status' => 'done']);

    $this->actingAs($user)->putJson("/api/tasks/{$task->id}", [
        'title'  => $task->title,
        'status' => 'done',
    ]);

    Event::assertNotDispatched(TaskCompleted::class);
});

it('owner can delete a task', function () {
    [$user, $workspace, $project] = projectWithMember();
    $task = Task::factory()->create(['project_id' => $project->id]);

    $response = $this->actingAs($user)->deleteJson("/api/tasks/{$task->id}");

    $response->assertStatus(204);
});
