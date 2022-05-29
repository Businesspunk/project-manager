<?php

namespace App\Tests\Controller;

use App\Security\UserProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testRedirectIfNotLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseRedirects('/login', 302);
    }

    public function testSuccessfulViewingHomePageWithLogin(): void
    {
        $client = static::createClient();
        $userProvider = static::getContainer()->get(UserProvider::class);

        $testUser = $userProvider->loadUserByUsername('admin@app.test');
        $client->loginUser($testUser);

        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', stripslashes('Homepage'));
    }
}
