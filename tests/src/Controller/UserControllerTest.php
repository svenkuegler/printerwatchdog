<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    const ADMIN_USER = "admin";
    const ADMIN_USER_PASSWORD = "123456";

    const NEW_USER = "new_test_user";
    const NEW_USER_PASSWORD = "123456";
    const NEW_USER_EMAIL = "new_test_user@domainname.com";

    protected function setUp(): void
    {
        $this->markTestIncomplete();
    }

    public function testUserCrud()
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

        // Goto User Index
        $linkCrawler = $crawler->selectLink('User');
        $crawler = $client->click($linkCrawler->link());

        // Create new User
        $linkCrawler2 = $crawler->selectLink('Create new');
        $crawler = $client->click($linkCrawler2->link());

        // Send User Form
        $userForm = $crawler->selectButton('Save')->form([
            "user[username]" => self::NEW_USER,
            "user[plainPassword]" => self::NEW_USER_PASSWORD,
            "user[email]" => self::NEW_USER_EMAIL,
        ]);
        $client->submit($userForm);
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertSelectorTextContains('td', self::NEW_USER_EMAIL);
        echo $client->getResponse()->getContent();

        // Activate

        // Deactivate


        // Delete
    }
}