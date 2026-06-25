<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can register a new user', function () {
    $response = $this->postJson('/api/auth/register', [
        'name'                  => 'Daniel',
        'email'                 => 'daniel@test.com',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure(['user', 'token']);
});

it('cannot register with duplicate email', function () {
    User::factory()->create(['email' => 'daniel@test.com']);

    $response = $this->postJson('/api/auth/register', [
        'name'                  => 'Daniel',
        'email'                 => 'daniel@test.com',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(422);
});

it('can login with valid credentials', function () {
    User::factory()->create([
        'email'    => 'daniel@test.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email'    => 'daniel@test.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['user', 'token']);
});

it('cannot login with invalid credentials', function () {
    $response = $this->postJson('/api/auth/login', [
        'email'    => 'wrong@test.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(401);
});

it('can logout', function () {
    $user = User::factory()->create();
    $token = $user->createToken('api-token')->plainTextToken;

    $response = $this->withToken($token)->postJson('/api/auth/logout');

    $response->assertStatus(200);
});
