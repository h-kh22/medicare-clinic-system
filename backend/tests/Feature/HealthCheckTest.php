<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    /**
     * Test the MediCare API health endpoint returns expected JSON structure.
     */
    public function test_health_check_returns_successful_response(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'MediCare API is running',
                'data' => [
                    'application' => 'MediCare',
                    'version' => '1.0.0',
                ],
            ]);
    }
}
