<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('unauthorized users cannot access admin panel', function () {
    $user = User::factory()->create(['role' => 'vendedor']);

    $response = $this->actingAs($user)->get(route('config.critical'));

    $response->assertForbidden();
});

test('admin users can access admin panel', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($user)->get(route('config.critical'));

    $response->assertOk();
});

test('guests are redirected to login', function () {
    $response = $this->get(route('dashboard'));

    $response->assertRedirect(route('login'));
});
