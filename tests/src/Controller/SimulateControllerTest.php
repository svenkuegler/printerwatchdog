<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SimulateControllerTest extends WebTestCase
{
    /**
     * Test Custom 404 Error Page
     */
    public function test404()
    {
        $client = static::createClient();

        $client->request('GET', '/_simulate/404');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains("h1", "Page not found");
    }

    /**
     * Test Custom 403 Error Page
     */
    public function test403()
    {
        $client = static::createClient();

        $client->request('GET', '/_simulate/403');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains("h1", "Access Denied");
    }

    /**
     * Test Custom 403 Error Page
     */
    public function test500()
    {
        $client = static::createClient();

        $client->request('GET', '/_simulate/500');

        $this->assertEquals(500, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains("h1", "Ooops! Something is going wrong!");
    }


}