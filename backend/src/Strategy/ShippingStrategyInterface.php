<?php

namespace App\Strategy;

interface ShippingStrategyInterface
{
    public function getSlug(): string;

    public function getName(): string;

    public function calculatePrice(float $weightKg): float;
}
