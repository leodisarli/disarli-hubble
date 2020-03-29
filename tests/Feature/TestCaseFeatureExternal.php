<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Testing\TestCase;
use ReflectionObject;

class TestCaseFeatureExternal extends TestCase
{
    protected $header;

    public function createApplication()
    {
        return require __DIR__.'/../../bootstrap/app.php';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'PartnerTokenSeeder']);
        $this->artisan('db:seed', ['--class' => 'CompanyTokenSeeder']);

        $body = [
            'partner_token' => 'd02e70e751726dd71962923b5d5195bbe0681f630ee92169d9891fd55d80ee94',
            'company_token' => '8660bfdfcad4e26d6405d36521798b414c6394d0a4e62a581cdd79fbf6682031',
        ];

        $this->json('POST', '/v1/auth', $body);

        $token = json_decode($this->response->getContent(), true)['data']['token'];

        $this->header = [
            'Authorization' => $token,
            'Context' => $body['company_token'],
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
