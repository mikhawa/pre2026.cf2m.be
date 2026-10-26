<?php

declare(strict_types=1);

namespace App\Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * La SPA React s'appuie sur ce point d'API pour savoir si le backend Symfony répond.
 */
class StatutControllerTest extends WebTestCase
{
    public function testStatutRenvoieOkEnJson(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/statut');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('Content-Type', 'application/json');
        self::assertSame(['statut' => 'ok'], json_decode((string) $client->getResponse()->getContent(), true));
    }

    public function testStatutRefuseLesAutresMethodes(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/statut');

        self::assertResponseStatusCodeSame(405);
    }
}
