<?php

namespace App\Tests\Integration\Infrastructure\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ShippingControllerTest extends WebTestCase
{
    public function testCalculateReturnsExpectedPayloadForTransCompany(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/shipping/calculate',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'carrier' => 'transcompany',
                'weightKg' => 12.5,
            ], JSON_THROW_ON_ERROR)
        );

        self::assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame('transcompany', $data['carrier']);
        self::assertSame(12.5, $data['weightKg']);
        self::assertSame('EUR', $data['currency']);
        self::assertEquals(100.0, $data['price']);
    }

    public function testCalculateReturnsValidationErrorForNegativeWeight(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/shipping/calculate',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'carrier' => 'packgroup',
                'weightKg' => -5,
            ], JSON_THROW_ON_ERROR)
        );

        self::assertResponseStatusCodeSame(400);

        $data = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('Validation failed', $data['error']);
        self::assertIsArray($data['details']);
        self::assertNotEmpty($data['details']);
    }

    public function testCalculateReturnsErrorForUnsupportedCarrier(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/shipping/calculate',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'carrier' => 'unknown',
                'weightKg' => 1,
            ], JSON_THROW_ON_ERROR)
        );

        self::assertResponseStatusCodeSame(400);

        $data = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('error', $data);
        self::assertStringContainsString('not supported', $data['error']);
    }
}
