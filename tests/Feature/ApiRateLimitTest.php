<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiRateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_public_routes_have_throttle_headers()
    {
        $response = $this->getJson('/api/courses');

        $response->assertHeader('X-RateLimit-Limit');
        $response->assertHeader('X-RateLimit-Remaining');
    }

    public function test_api_courses_endpoint_is_accessible()
    {
        $response = $this->getJson('/api/courses');

        $response->assertOk();
    }
}
