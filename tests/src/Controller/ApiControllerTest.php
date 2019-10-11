<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiControllerTest extends WebTestCase
{

    protected function setUp(): void
    {
        $this->markTestIncomplete();
    }

    public function testSuccessfulApiCall()
    {
        $client = static::createClient();

        $client->request('GET', '/api/request-single-value/{value}/{id}');
        $this->assertEquals(true, false);
    }

    public function testUnknownPrinter()
    {
        $client = static::createClient();

        $client->request('GET', '/api/request-single-value/127.0.0.1/.1.3.6.1.2.1.1.1.0');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('{"status":500,"errorMessage":"printer unknown!"}', $client->getResponse()->getContent());
    }

    public function testUnknownValue()
    {
        $client = static::createClient();

        $client->request('GET', '/api/request-single-value/127.0.0.1/.9.9.9');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('{"status":500,"errorMessage":"value unknown!"}', $client->getResponse()->getContent());
    }
}