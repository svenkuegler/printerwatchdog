<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MonitoringTest extends WebTestCase
{
    /**
     * Test simple status API call for JSON response
     */
    public function testStatusApiCall()
    {
        $client = static::createClient();
        $client->request('GET', '/monitoring/status');
        $this->assertJson($client->getResponse()->getContent());
    }

    /**
     * Test Permission of Monitoring Page
     */
    public function testPermissionMonitoringPage()
    {
        $client = static::createClient();
        $client->request('GET', '/monitoring');
        $this->assertResponseRedirects('/login');
    }

    /**
     * @todo build test after implement printer DataFixtures
     */
    public function testWarningMessage() {
        $this->markTestIncomplete();
    }

    /**
     * @todo build test after implement printer DataFixtures
     */
    public function testCriticalMessage() {
        $this->markTestIncomplete();
    }
}