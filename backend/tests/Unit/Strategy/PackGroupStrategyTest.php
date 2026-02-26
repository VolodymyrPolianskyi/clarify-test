<?php

namespace App\Tests\Unit\Strategy;

use App\Strategy\PackGroupStrategy;
use PHPUnit\Framework\TestCase;

class PackGroupStrategyTest extends TestCase
{
    private PackGroupStrategy $strategy;

    protected function setUp(): void
    {
        $this->strategy = new PackGroupStrategy();
    }

    public function testGetSlug(): void
    {
        $this->assertSame('packgroup', $this->strategy->getSlug());
    }

    public function testGetName(): void
    {
        $this->assertSame('PackGroup', $this->strategy->getName());
    }

    /** @dataProvider weightPriceProvider */
    public function testPriceIsOneEurPerKg(float $weightKg, float $expectedPrice): void
    {
        $this->assertSame($expectedPrice, $this->strategy->calculatePrice($weightKg));
    }

    public static function weightPriceProvider(): array
    {
        return [
            '1 kg → 1 EUR'    => [1.0,  1.0],
            '5 kg → 5 EUR'    => [5.0,  5.0],
            '10 kg → 10 EUR'  => [10.0, 10.0],
            '12.5 kg → 12.5 EUR' => [12.5, 12.5],
            '100 kg → 100 EUR'   => [100.0, 100.0],
        ];
    }
}
