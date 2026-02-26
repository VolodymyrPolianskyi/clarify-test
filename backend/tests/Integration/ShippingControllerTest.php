<?php

namespace App\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Интеграционные тесты для API эндпоинтов.
 * WebTestCase поднимает реальный Symfony kernel и делает HTTP запросы внутри.
 */
class ShippingControllerTest extends WebTestCase
{
    // -------------------------------------------------------------------------
    // GET /api/carriers
    // -------------------------------------------------------------------------

    public function testGetCarriersReturns200(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/carriers');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
    }

    public function testGetCarriersReturnsJson(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/carriers');

        $this->assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testGetCarriersContainsTranscompanyAndPackgroup(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/carriers');

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('carriers', $data);

        $slugs = array_column($data['carriers'], 'slug');
        $this->assertContains('transcompany', $slugs);
        $this->assertContains('packgroup', $slugs);
    }

    // -------------------------------------------------------------------------
    // POST /api/shipping/calculate — успешные кейсы
    // -------------------------------------------------------------------------

    public function testCalculateTranscompanyLightParcel(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/shipping/calculate', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['carrier' => 'transcompany', 'weightKg' => 5.0]));

        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('transcompany', $data['carrier']);
        $this->assertSame(5.0, $data['weightKg']);
        $this->assertSame('EUR', $data['currency']);
        $this->assertSame(20.0, $data['price']);
    }

    public function testCalculateTranscompanyHeavyParcel(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/shipping/calculate', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['carrier' => 'transcompany', 'weightKg' => 12.5]));

        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame(100.0, $data['price']);
    }

    public function testCalculatePackgroup(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/shipping/calculate', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['carrier' => 'packgroup', 'weightKg' => 7.0]));

        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame(7.0, $data['price']);
    }

    public function testCalculateResponseHasAllFields(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/shipping/calculate', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['carrier' => 'packgroup', 'weightKg' => 3.0]));

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('carrier', $data);
        $this->assertArrayHasKey('weightKg', $data);
        $this->assertArrayHasKey('currency', $data);
        $this->assertArrayHasKey('price', $data);
    }

    // -------------------------------------------------------------------------
    // POST /api/shipping/calculate — ошибки
    // -------------------------------------------------------------------------

    public function testCalculateUnknownCarrierReturns422(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/shipping/calculate', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['carrier' => 'unknown', 'weightKg' => 5.0]));

        $this->assertResponseStatusCodeSame(422);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $data);
        $this->assertSame('Unsupported carrier', $data['error']);
    }

    public function testCalculateMissingCarrierReturns422(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/shipping/calculate', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['weightKg' => 5.0]));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCalculateNegativeWeightReturns422(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/shipping/calculate', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['carrier' => 'transcompany', 'weightKg' => -1.0]));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCalculateZeroWeightReturns422(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/shipping/calculate', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['carrier' => 'transcompany', 'weightKg' => 0]));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCalculateInvalidJsonReturns400(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/shipping/calculate', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], 'not valid json');

        $this->assertResponseStatusCodeSame(400);
    }
}