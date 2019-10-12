<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use function foo\func;

class UserControllerTest extends WebTestCase
{
    const ADMIN_USER = "admin";
    const ADMIN_USER_PASSWORD = "123456";

    private $NEW_USER = "new_test_user_%s";
    private $NEW_USER_PASSWORD = "123456";
    private $NEW_USER_EMAIL = "new_test_user_%s@domainname.com";

    protected function setUp(): void
    {
        $t = time();
        $this->NEW_USER = sprintf($this->NEW_USER, $t);
        $this->NEW_USER_EMAIL  = sprintf($this->NEW_USER_EMAIL, $t);

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
            "user[username]" => $this->NEW_USER,
            "user[plainPassword]" => $this->NEW_USER_PASSWORD,
            "user[email]" => $this->NEW_USER_EMAIL,
        ]);
        $client->submit($userForm);
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();

        /*
         * TODO:
         *  - check (write is successful? currently it is but unchecked!)
         *  - activate the new user -> and check
         *  - deactivate new user -> and check
         *  - delete new user -> and check
         */

        echo $client->getResponse()->getContent();
        $this->assertSelectorTextContains('tr', $this->NEW_USER_EMAIL);



        //var_dump($crawler->filter("td")->children());

        // Activate

        // Deactivate


        // Delete
    }
}