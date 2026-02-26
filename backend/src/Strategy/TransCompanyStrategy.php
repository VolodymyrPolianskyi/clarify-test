<?php

namespace App\Strategy;

class TransCompanyStrategy implements ShippingStrategyInterface
{
    private const LIGHT_THRESHOLD_KG = 10.0;
    private const LIGHT_PRICE_EUR = 20.0;
    private const HEAVY_PRICE_EUR = 100.0;

    public function getSlug(): string
    {
        return 'transcompany';
    }

    public function getName(): string
    {
        return 'TransCompany';
    }

    public function calculatePrice(float $weightKg): float
    {
        if ($weightKg <= self::LIGHT_THRESHOLD_KG) {
            return self::LIGHT_PRICE_EUR;
        }

        return self::HEAVY_PRICE_EUR;
    }
}
