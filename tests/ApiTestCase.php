<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiTestCase extends WebTestCase
{
    protected $client;

    public function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    public function getResponseData($response): array
    {
        return json_decode($response->getContent(), true);
    }
}
