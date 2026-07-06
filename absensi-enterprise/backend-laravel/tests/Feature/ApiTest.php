<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiTest extends TestCase
{
    public function test_api_login_endpoint_exists(): void
    {
        $response = $this->postJson('/api/login', [
            'nip' => '99999',
            'password' => 'wrongpassword',
        ]);

        // Endpoint ada dan merespons (401 = endpoint jalan, bukan 404)
        $response->assertStatus(401);
        $response->assertJson(['status' => 'error']);
    }

    public function test_api_returns_json(): void
    {
        $response = $this->postJson('/api/login', []);
        $response->assertHeader('Content-Type', 'application/json');
    }
}