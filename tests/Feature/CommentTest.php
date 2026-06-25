<?php

use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function taskWithMember(): array
{
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
    $workspace->members()->attach($user->id, ['role' => 'owner']);
    $project = Project::factory()->create(['workspace_id' => $workspace->id]);
    $task = Task::factory()->create(['project_id' => $project->id, 'status' => 'todo']);

    return [$user, $task];
}

it('member can create a comment', function () {
    [$user, $task] = taskWithMember();

    $response = $this->actingAs($user)->postJson("/api/tasks/{$task->id}/comments", [
        'body' => 'Meu comentário',
    ]);

    $response->assertStatus(201)
        ->assertJsonFragment(['body' => 'Meu comentário']);
});

it('member can list comments', function () {
    [$user, $task] = taskWithMember();
    Comment::factory()->create(['task_id' => $task->id, 'user_id' => $user->id]);

    $response = $this->actingAs($user)->getJson("/api/tasks/{$task->id}/comments");

    $response->assertStatus(200)->assertJsonCount(1);
});

it('author can update own comment', function () {
    [$user, $task] = taskWithMember();
    $comment = Comment::factory()->create(['task_id' => $task->id, 'user_id' => $user->id]);

    $response = $this->actingAs($user)->putJson("/api/comments/{$comment->id}", [
        'body' => 'Comentário editado',
    ]);

    $response->assertStatus(200)
        ->assertJsonFragment(['body' => 'Comentário editado']);
});

it('non author cannot update comment', function () {
    [$user, $task] = taskWithMember();
    $other = User::factory()->create();
    $comment = Comment::factory()->create(['task_id' => $task->id, 'user_id' => $other->id]);

    $response = $this->actingAs($user)->putJson("/api/comments/{$comment->id}", [
        'body' => 'Tentativa',
    ]);

    $response->assertStatus(403);
});

it('author can delete own comment', function () {
    [$user, $task] = taskWithMember();
    $comment = Comment::factory()->create(['task_id' => $task->id, 'user_id' => $user->id]);

    $response = $this->actingAs($user)->deleteJson("/api/comments/{$comment->id}");

    $response->assertStatus(204);
});
