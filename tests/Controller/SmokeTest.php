<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SmokeTest extends WebTestCase
{
    public function testApiDocUrlIsSuccessful(): void
    {
        $client = self::createClient();
        $client->request('GET', '/api/doc');

        self::assertResponseIsSuccessful();
    }

    /*
    public function testAccountUrlIsSecure(): void
    {
        $client = self::createClient();
        $client->request('GET', 'api/account/me');

        self::assertResponseStatusCodeSame(401);
    }
    */

    public function testLoginRouteCanConnectAVadlidUser(): void
    {
        $client = self::createClient();
        $client->followRedirects(false);

        $client->request('POST', '/api/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'username' => 'toto@toto.fr',
            'password' => 'toto',
        ], JSON_THROW_ON_ERROR));

        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertEquals(200, $statusCode);
    }

    public function testLoginRouteCanConnectANoVadlidUser(): void
    {
        $client = self::createClient();
        $client->followRedirects(false);

        $client->request('POST', '/api/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'username' => 'novalid@toto.fr',
            'password' => 'novalid',
        ], JSON_THROW_ON_ERROR));

        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertEquals(401, $statusCode);
    }
}
