<?php

namespace App\Tests\Unit\Strategy;

use App\Strategy\TransCompanyStrategy;
use PHPUnit\Framework\TestCase;

class TransCompanyStrategyTest extends TestCase
{
    private TransCompanyStrategy $strategy;

    protected function setUp(): void
    {
        $this->strategy = new TransCompanyStrategy();
    }

    public function testGetSlug(): void
    {
        $this->assertSame('transcompany', $this->strategy->getSlug());
    }

    public function testGetName(): void
    {
        $this->assertSame('TransCompany', $this->strategy->getName());
    }

    /** @dataProvider lightParcelProvider */
    public function testLightParcelCosts20Eur(float $weightKg): void
    {
        $this->assertSame(20.0, $this->strategy->calculatePrice($weightKg));
    }

    public static function lightParcelProvider(): array
    {
        return [
            'minimum weight'  => [0.1],
            'exactly 1 kg'    => [1.0],
            'exactly 5 kg'    => [5.0],
            'exactly 10 kg'   => [10.0],   // граница — должно быть 20
        ];
    }

    /** @dataProvider heavyParcelProvider */
    public function testHeavyParcelCosts100Eur(float $weightKg): void
    {
        $this->assertSame(100.0, $this->strategy->calculatePrice($weightKg));
    }

    public static function heavyParcelProvider(): array
    {
        return [
            'just above 10 kg' => [10.01],
            '12.5 kg'          => [12.5],
            '50 kg'            => [50.0],
            '100 kg'           => [100.0],
        ];
    }
}
