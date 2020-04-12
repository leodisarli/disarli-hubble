<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Testing\TestCase;
use ReflectionObject;

class TestCaseFeature extends TestCase
{
    protected $header;

    public function createApplication()
    {
        return require __DIR__.'/../../bootstrap/app.php';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $credencials = [
            'token' => 'cdf06d00ecaf34c2d85ac60068ad6b86af198340cf7e289c3c33bd95de8f985a',
            'secret' => '6b1ed4ec8034dba1ac1a9d30a20a276255f678c78efb66f4262a9f7c1d1231c9',
        ];

        $this->json('POST', '/auth/generate', $credencials);

        $token = json_decode($this->response->getContent(), true)['data']['token'];

        $this->header = [
            'Authorization' => $token,
            'Context' => 'dimi-dev',
        ];
    }

    protected function tearDown(): void
    {
        $refl = new ReflectionObject($this);
        foreach ($refl->getProperties() as $prop) {
            if (!$prop->isStatic() && 0 !== strpos($prop->getDeclaringClass()->getName(), 'PHPUnit_')) {
                $prop->setAccessible(true);
                $prop->setValue($this, null);
            }
        }
        DB::disconnect();
        parent::tearDown();
    }
}
