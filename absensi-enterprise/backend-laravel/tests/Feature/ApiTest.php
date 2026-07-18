<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiTest extends TestCase
{
    use RefreshDatabase; // otomatis jalankan migration setiap test

    // Test 1: Response selalu JSON
    public function test_api_returns_json(): void
    {
        $response = $this->postJson('/api/login', []);
        $response->assertHeader('Content-Type', 'application/json');
    }

    // Test 2: Health check endpoint tersedia dan strukturnya benar
    public function test_health_endpoint(): void
    {
        $response = $this->getJson('/api/health');
        $response->assertStatus(200);
        $response->assertJson(['status' => 'healthy']);
        $response->assertJsonStructure([
            'status',
            'service',
            'database',
            'timestamp',
        ]);
    }

    // Test 3: Login tanpa input ditolak dengan validasi 422
    public function test_api_login_validates_input(): void
    {
        $response = $this->postJson('/api/login', []);
        $response->assertStatus(422);
    }

    // Test 4: Login dengan kredensial salah ditolak 401
    public function test_api_login_wrong_credentials(): void
    {
        $response = $this->postJson('/api/login', [
            'nip'      => '00000',
            'password' => 'wrongpassword',
        ]);
        $response->assertStatus(401);
        $response->assertJson(['status' => 'error']);
    }

    // Test 5: Endpoint yang butuh auth ditolak tanpa token
    public function test_protected_endpoint_requires_token(): void
    {
        $response = $this->getJson('/api/attendances/history');
        $response->assertStatus(401);
    }
}