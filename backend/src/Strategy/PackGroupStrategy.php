<?php

namespace App\Strategy;

class PackGroupStrategy implements ShippingStrategyInterface
{
    private const PRICE_PER_KG = 1.0;

    public function getSlug(): string
    {
        return 'packgroup';
    }

    public function getName(): string
    {
        return 'PackGroup';
    }

    public function calculatePrice(float $weightKg): float
    {
        return $weightKg * self::PRICE_PER_KG;
    }
}
