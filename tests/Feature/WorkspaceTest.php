<?php

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create a workspace', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/workspaces', [
        'name' => 'Meu Workspace',
    ]);

    $response->assertStatus(201)
        ->assertJsonFragment(['name' => 'Meu Workspace']);
});

it('can list own workspaces', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
    $workspace->members()->attach($user->id, ['role' => 'owner']);

    $response = $this->actingAs($user)->getJson('/api/workspaces');

    $response->assertStatus(200)
        ->assertJsonFragment(['name' => $workspace->name]);
});

it('can view a workspace as member', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
    $workspace->members()->attach($user->id, ['role' => 'owner']);

    $response = $this->actingAs($user)->getJson("/api/workspaces/{$workspace->id}");

    $response->assertStatus(200);
});

it('cannot view workspace if not a member', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create();

    $response = $this->actingAs($user)->getJson("/api/workspaces/{$workspace->id}");

    $response->assertStatus(403);
});

it('owner can update workspace', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create(['owner_id' => $user->id]);

    $response = $this->actingAs($user)->putJson("/api/workspaces/{$workspace->id}", [
        'name' => 'Novo Nome',
    ]);

    $response->assertStatus(200)
        ->assertJsonFragment(['name' => 'Novo Nome']);
});

it('member cannot update workspace', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $workspace = Workspace::factory()->create(['owner_id' => $owner->id]);
    $workspace->members()->attach($member->id, ['role' => 'member']);

    $response = $this->actingAs($member)->putJson("/api/workspaces/{$workspace->id}", [
        'name' => 'Tentativa',
    ]);

    $response->assertStatus(403);
});

it('owner can delete workspace', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create(['owner_id' => $user->id]);

    $response = $this->actingAs($user)->deleteJson("/api/workspaces/{$workspace->id}");

    $response->assertStatus(204);
});

it('owner can add a member', function () {
    $owner = User::factory()->create();
    $newMember = User::factory()->create();
    $workspace = Workspace::factory()->create(['owner_id' => $owner->id]);

    $response = $this->actingAs($owner)->postJson("/api/workspaces/{$workspace->id}/members", [
        'email' => $newMember->email,
    ]);

    $response->assertStatus(201);
});

it('owner can remove a member', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $workspace = Workspace::factory()->create(['owner_id' => $owner->id]);
    $workspace->members()->attach($member->id, ['role' => 'member']);

    $response = $this->actingAs($owner)->deleteJson("/api/workspaces/{$workspace->id}/members/{$member->id}");

    $response->assertStatus(204);
});
