<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ShippingCalculateRequest
{
    #[Assert\NotBlank(message: 'Carrier is required')]
    #[Assert\Type('string')]
    public readonly string $carrier;

    #[Assert\NotNull(message: 'weightKg is required')]
    #[Assert\Type(type: 'numeric', message: 'weightKg must be a number')]
    #[Assert\Positive(message: 'weightKg must be a positive number')]
    #[Assert\LessThanOrEqual(value: 10000, message: 'weightKg cannot exceed 10000 kg')]
    public readonly float $weightKg;

    public function __construct(string $carrier, float $weightKg)
    {
        $this->carrier = $carrier;
        $this->weightKg = $weightKg;
    }
}
