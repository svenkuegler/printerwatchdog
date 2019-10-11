<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NotificationControllerTest extends WebTestCase
{
    const ADMIN_USER = "admin";
    const ADMIN_USER_PASSWORD = "123456";

    protected function setUp()
    {
        $this->markTestIncomplete();
    }

    public function testNotificationChanges()
    {
       /* $client = static::createClient();
        $client->followRedirects();

        // login
        $crawler = $client->request('GET', '/login');
        $loginForm = $crawler->selectButton("Sign in")->form();
        $loginForm["username"] = self::ADMIN_USER;
        $loginForm["password"] = self::ADMIN_USER_PASSWORD;
        $crawler = $client->submit($loginForm);

        $this->assertEquals(200, $client->getResponse()->isOk());

        // change
        $crawler = $client->request('GET', '/notification');
        $notifyForm = $crawler->selectButton('__save changes')->form();
        $notifyForm["webWarning"] = 8;
        $notifyForm["webDanger"] = 3;
        $notifyForm["webWarning"] = 8;
        $notifyForm["webDanger"] = 3;
        $notifyForm["webWarning"] = 8;
        $notifyForm["webDanger"] = 3;

        $crawler = $client->submit($notifyForm);
        var_dump($crawler->html());
        //$client->submitForm("", );


        // test changes

        // reset*/


    }
}