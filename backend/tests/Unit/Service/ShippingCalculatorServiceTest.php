<?php

namespace App\Tests\Unit\Service;

use App\Exception\UnsupportedCarrierException;
use App\Service\ShippingCalculatorService;
use App\Strategy\PackGroupStrategy;
use App\Strategy\TransCompanyStrategy;
use PHPUnit\Framework\TestCase;

class ShippingCalculatorServiceTest extends TestCase
{
    private ShippingCalculatorService $service;

    protected function setUp(): void
    {
        $this->service = new ShippingCalculatorService([
            new TransCompanyStrategy(),
            new PackGroupStrategy(),
        ]);
    }

    public function testCalculateWithTransCompanyLightParcel(): void
    {
        $price = $this->service->calculate('transcompany', 5.0);
        $this->assertSame(20.0, $price);
    }

    public function testCalculateWithTransCompanyHeavyParcel(): void
    {
        $price = $this->service->calculate('transcompany', 12.5);
        $this->assertSame(100.0, $price);
    }

    public function testCalculateWithPackGroup(): void
    {
        $price = $this->service->calculate('packgroup', 7.0);
        $this->assertSame(7.0, $price);
    }

    public function testThrowsExceptionForUnknownCarrier(): void
    {
        $this->expectException(UnsupportedCarrierException::class);
        $this->service->calculate('unknowncarrier', 5.0);
    }

    public function testGetAvailableCarriersReturnsAllCarriers(): void
    {
        $carriers = $this->service->getAvailableCarriers();

        $this->assertCount(2, $carriers);

        $slugs = array_column($carriers, 'slug');
        $this->assertContains('transcompany', $slugs);
        $this->assertContains('packgroup', $slugs);
    }

    public function testGetAvailableCarriersHasNameField(): void
    {
        $carriers = $this->service->getAvailableCarriers();

        foreach ($carriers as $carrier) {
            $this->assertArrayHasKey('slug', $carrier);
            $this->assertArrayHasKey('name', $carrier);
            $this->assertNotEmpty($carrier['name']);
        }
    }
}
