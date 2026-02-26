<?php

namespace App\Service;

use App\Exception\UnsupportedCarrierException;
use App\Strategy\ShippingStrategyInterface;

class ShippingCalculatorService
{
    private array $strategies = [];

    public function __construct(iterable $strategies)
    {
        foreach ($strategies as $strategy) {
            $this->strategies[$strategy->getSlug()] = $strategy;
        }
    }

    public function calculate(string $carrierSlug, float $weightKg): float
    {
        $strategy = $this->strategies[$carrierSlug] ?? null;

        if ($strategy === null) {
            throw new UnsupportedCarrierException($carrierSlug);
        }

        return $strategy->calculatePrice($weightKg);
    }

    public function getAvailableCarriers(): array
    {
        return array_values(
            array_map(
                fn(ShippingStrategyInterface $s) => [
                    'slug' => $s->getSlug(),
                    'name' => $s->getName(),
                ],
                $this->strategies
            )
        );
    }
}
