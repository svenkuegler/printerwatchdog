<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProfileControllerTest extends WebTestCase
{
    const TEST_USER = "testuser";
    const TEST_USER_PASSWORD = "123456";
    const TEST_USER_NEW_PASSWORD = "abcdefg";

    protected function setUp(): void
    {
        //$this->markTestIncomplete();
    }

    public function testChangePasswordAndLocaleToGerman()
    {
        $client = static::createClient();

        // Login
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form([
            "username" => self::TEST_USER,
            "password" => self::TEST_USER_PASSWORD
        ],'POST');
        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();

        // Goto Profile
        $linkCrawler = $crawler->selectLink('Profile');
        $crawler = $client->click($linkCrawler->link());

        // Send Profile Form
        $userForm = $crawler->selectButton('Update')->form([
            "profile[plainPassword]" => self::TEST_USER_NEW_PASSWORD,
            "profile[locale]" => 'de',
        ]);
        $client->submit($userForm);
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertSelectorTextContains('div', "Benutzer");

        $linkCrawler2 = $crawler->selectLink('Abmelden');
        $crawler = $client->click($linkCrawler2->link());
        $this->assertTrue($client->getResponse()->isRedirect());
    }

    public function testChangePasswordAndLocaleBackToEnglish()
    {
        $client = static::createClient();

        // Login
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Sign in')->form([
            "username" => self::TEST_USER,
            "password" => self::TEST_USER_NEW_PASSWORD
        ],'POST');
        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();

        // Goto Profile
        $linkCrawler = $crawler->selectLink('Mein Profil');
        $crawler = $client->click($linkCrawler->link());

        // Send Profile Form
        $userForm = $crawler->selectButton('Update')->form([
            "profile[plainPassword]" => self::TEST_USER_PASSWORD,
            "profile[locale]" => 'en',
        ]);
        $client->submit($userForm);
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertSelectorTextContains('div', "User");
    }
}