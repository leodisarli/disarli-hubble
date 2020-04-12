<?php

namespace Tests\Feature\Domains\Health;

use Exception;
use Illuminate\Support\Facades\Config;
use Tests\Feature\TestCaseFeature;

class HealthDbControllerTest extends TestCaseFeature
{
    /**
     * @covers \App\Domains\Health\Http\Controllers\HealthDbController::__construct
     * @covers \App\Domains\Health\Http\Controllers\HealthDbController::process
     */
    public function testHealthDb()
    {
        $this->json('GET', '/health/db', []);
        $result = json_decode(
            $this->response->getContent(),
            true
        );

        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertArrayHasKey('status', $result);
        $this->assertEquals(
            $result['status'],
            'online'
        );
        $this->assertArrayHasKey('version', $result);
    }

    /**
     * @covers \App\Domains\Health\Http\Controllers\HealthDbController::process
     */
    public function testUnhealthDb()
    {
        Config::set('elasticsearch.host', 'hubble-elastic:92001');
        $this->json('GET', '/health/db', []);
        $result = json_decode(
            $this->response->getContent(),
            true
        );

        $this->assertEquals(500, $this->response->getStatusCode());
        $this->assertArrayHasKey('message', $result);
        $this->assertEquals(
            $result['message'],
            'An unexpected error occurred, please try again later'
        );
    }
}
