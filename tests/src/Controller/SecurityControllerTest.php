<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{

    const ADMIN_USER = "admin";
    const ADMIN_USER_PASSWORD = "123456";
    const ADMIN_USER_BAD_PASSWORD = "bad-password";

    const TEST_USER = "testuser";
    const TEST_USER_PASSWORD = "123456";

    const INVALID_USER = "testuserinactive";
    const INVALID_USER_PASSWORD = "123456";


    public function testLoginPage()
    {
        $client = static::createClient();

        $client->request('GET', '/login');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testLoginSuccessfull()
    {
        $client = static::createClient();

        $client->request('GET', '/login');
        $client->submitForm("Sign in", [
           "username" => self::ADMIN_USER,
           "password" => self::ADMIN_USER_PASSWORD
        ]);

        $this->assertResponseRedirects('/dashboard');
    }

    public function testLoginFailed()
    {
        $client = static::createClient();
        $client->followRedirects();

        $client->request('GET', '/login');
        $client->submitForm("Sign in", [
            "username" => self::ADMIN_USER,
            "password" => self::ADMIN_USER_BAD_PASSWORD
        ]);
        $this->assertSelectorTextContains('form div.alert', "Invalid credentials.");
    }

    public function testLoginInactive()
    {
        $client = static::createClient();
        $client->followRedirects();

        $client->request('GET', '/login');
        $client->submitForm("Sign in", [
            "username" => self::INVALID_USER,
            "password" => self::INVALID_USER_PASSWORD
        ]);
        $this->assertSelectorTextContains('form div.alert', "Username could not be found or User is not active.");
    }

    public function testPermission()
    {
        $client = static::createClient();
        $container = self::$container;
        $client->followRedirects();

        $client->request('GET', '/login');
        $client->submitForm("Sign in", [
            "username" => self::TEST_USER,
            "password" => self::TEST_USER_PASSWORD
        ]);
        $this->assertEquals(false, $container->get("security.authorization_checker")->isGranted("ROLE_ADMIN"));
    }

    public function testLogout()
    {
        $client = static::createClient();
        $container = self::$container;
        $client->followRedirects();

        $client->request('GET', '/login');
        $client->submitForm("Sign in", [
            "username" => self::ADMIN_USER,
            "password" => self::ADMIN_USER_PASSWORD
        ]);
        $this->assertEquals(true, $container->get("security.authorization_checker")->isGranted("ROLE_ADMIN"));

        $client->request('GET', '/logout');
        $this->assertEquals(false, $container->get("security.authorization_checker")->isGranted("ROLE_ADMIN"));
    }
}