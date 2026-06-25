<?php

use App\Models\User;
use App\Models\Webhook;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function workspaceWithOwner(): array
{
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create(['owner_id' => $user->id]);

    return [$user, $workspace];
}

it('owner can create a webhook', function () {
    [$user, $workspace] = workspaceWithOwner();

    $response = $this->actingAs($user)->postJson("/api/workspaces/{$workspace->id}/webhooks", [
        'url'    => 'https://webhook.site/test',
        'events' => ['task.created'],
    ]);

    $response->assertStatus(201)
        ->assertJsonFragment(['url' => 'https://webhook.site/test']);
});

it('owner can list webhooks', function () {
    [$user, $workspace] = workspaceWithOwner();
    Webhook::factory()->create(['workspace_id' => $workspace->id]);

    $response = $this->actingAs($user)->getJson("/api/workspaces/{$workspace->id}/webhooks");

    $response->assertStatus(200)->assertJsonCount(1);
});

it('owner can view a webhook', function () {
    [$user, $workspace] = workspaceWithOwner();
    $webhook = Webhook::factory()->create(['workspace_id' => $workspace->id]);

    $response = $this->actingAs($user)->getJson("/api/webhooks/{$webhook->id}");

    $response->assertStatus(200);
});

it('owner can update a webhook', function () {
    [$user, $workspace] = workspaceWithOwner();
    $webhook = Webhook::factory()->create(['workspace_id' => $workspace->id]);

    $response = $this->actingAs($user)->putJson("/api/webhooks/{$webhook->id}", [
        'url'    => 'https://webhook.site/updated',
        'events' => ['task.completed'],
        'active' => false,
    ]);

    $response->assertStatus(200)
        ->assertJsonFragment(['active' => false]);
});

it('owner can delete a webhook', function () {
    [$user, $workspace] = workspaceWithOwner();
    $webhook = Webhook::factory()->create(['workspace_id' => $workspace->id]);

    $response = $this->actingAs($user)->deleteJson("/api/webhooks/{$webhook->id}");

    $response->assertStatus(204);
});

it('member cannot manage webhooks', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $workspace = Workspace::factory()->create(['owner_id' => $owner->id]);
    $workspace->members()->attach($member->id, ['role' => 'member']);

    $response = $this->actingAs($member)->postJson("/api/workspaces/{$workspace->id}/webhooks", [
        'url'    => 'https://webhook.site/test',
        'events' => ['task.created'],
    ]);

    $response->assertStatus(403);
});

it('cannot create webhook with invalid event', function () {
    [$user, $workspace] = workspaceWithOwner();

    $response = $this->actingAs($user)->postJson("/api/workspaces/{$workspace->id}/webhooks", [
        'url'    => 'https://webhook.site/test',
        'events' => ['evento.invalido'],
    ]);

    $response->assertStatus(422);
});
