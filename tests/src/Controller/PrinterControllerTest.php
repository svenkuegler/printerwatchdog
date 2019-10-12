<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PrinterControllerTest extends WebTestCase
{
    protected function setUp()
    {
        $this->markTestIncomplete();
    }

    public function testCreatePrinter()
    {
        $client = static::createClient();
        $this->assertEquals(true, false);
    }

    public function testBulkCreatePrinter()
    {
        $client = static::createClient();
        $this->assertEquals(true, false);
    }

    public function testEditPrinter()
    {
        $client = static::createClient();
        $this->assertEquals(true, false);
    }

    public function testDeletePrinter()
    {
        $client = static::createClient();
        $this->assertEquals(true, false);
    }
}