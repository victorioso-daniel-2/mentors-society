<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_roles_route_requires_authentication()
    {
        $response = $this->getJson('/api/roles');

        $response->assertStatus(401);
    }

    public function test_users_route_requires_authentication()
    {
        $response = $this->getJson('/api/users');

        $response->assertStatus(401);
    }

    public function test_students_route_requires_authentication()
    {
        $response = $this->getJson('/api/students');

        $response->assertStatus(401);
    }
} 