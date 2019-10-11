<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NotificationControllerTest extends WebTestCase
{
    const ADMIN_USER = "admin";
    const ADMIN_USER_PASSWORD = "123456";

    protected function setUp()
    {
        //$this->markTestIncomplete();
    }

    public function testNotificationChanges()
    {
        $client = static::createClient();

        // Login
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form([
            "username" => self::ADMIN_USER,
            "password" => self::ADMIN_USER_PASSWORD
        ],'POST');
        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();

        // Goto Notification
        $linkCrawler = $crawler->selectLink('Notification');
        $crawler = $client->click($linkCrawler->link());

        // Send Notification Form
        $notificationForm = $crawler->selectButton('save changes')->form([
            'webWarning' => 30,
            'webDanger' => 30,
            'emailWarning' => 30,
            'emailDanger' => 30,
            'slackWarning' => 30,
            'slackDanger' => 30
        ]);
        $client->submit($notificationForm);
        $this->assertSelectorTextContains("div.alert", "notification settings saved!");

    }

    public function testRestoreSettings()
    {
        $client = static::createClient();

        // Login
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form([
            "username" => self::ADMIN_USER,
            "password" => self::ADMIN_USER_PASSWORD
        ],'POST');
        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();

        // Goto Notification
        $linkCrawler = $crawler->selectLink('Notification');
        $crawler = $client->click($linkCrawler->link());

        // Send Notification Form
        $notificationForm = $crawler->selectButton('save changes')->form([
            'webWarning' => 30,
            'webDanger' => 10,
            'emailWarning' => 30,
            'emailDanger' => 10,
            'slackWarning' => 30,
            'slackDanger' => 10
        ]);
        $client->submit($notificationForm);
        $this->assertSelectorTextContains("div.alert", "notification settings saved!");

    }
}