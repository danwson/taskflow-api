<?php

use App\Models\Project;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function workspaceWithMember(): array
{
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
    $workspace->members()->attach($user->id, ['role' => 'owner']);

    return [$user, $workspace];
}

it('member can create a project', function () {
    [$user, $workspace] = workspaceWithMember();

    $response = $this->actingAs($user)->postJson("/api/workspaces/{$workspace->id}/projects", [
        'name' => 'Novo Projeto',
    ]);

    $response->assertStatus(201)
        ->assertJsonFragment(['name' => 'Novo Projeto']);
});

it('member can list projects', function () {
    [$user, $workspace] = workspaceWithMember();
    Project::factory()->create(['workspace_id' => $workspace->id]);

    $response = $this->actingAs($user)->getJson("/api/workspaces/{$workspace->id}/projects");

    $response->assertStatus(200)->assertJsonCount(1);
});

it('member can view a project', function () {
    [$user, $workspace] = workspaceWithMember();
    $project = Project::factory()->create(['workspace_id' => $workspace->id]);

    $response = $this->actingAs($user)->getJson("/api/projects/{$project->id}");

    $response->assertStatus(200);
});

it('member can update a project', function () {
    [$user, $workspace] = workspaceWithMember();
    $project = Project::factory()->create(['workspace_id' => $workspace->id]);

    $response = $this->actingAs($user)->putJson("/api/projects/{$project->id}", [
        'name' => 'Projeto Atualizado',
    ]);

    $response->assertStatus(200)
        ->assertJsonFragment(['name' => 'Projeto Atualizado']);
});

it('owner can delete a project', function () {
    [$user, $workspace] = workspaceWithMember();
    $project = Project::factory()->create(['workspace_id' => $workspace->id]);

    $response = $this->actingAs($user)->deleteJson("/api/projects/{$project->id}");

    $response->assertStatus(204);
});

it('non member cannot access projects', function () {
    $outsider = User::factory()->create();
    $workspace = Workspace::factory()->create();

    $response = $this->actingAs($outsider)->getJson("/api/workspaces/{$workspace->id}/projects");

    $response->assertStatus(403);
});
