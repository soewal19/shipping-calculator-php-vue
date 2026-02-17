<?php

namespace App\Infrastructure\Strategy;

use App\Domain\ShippingStrategyInterface;

class PackGroupStrategy implements ShippingStrategyInterface
{
    public function calculate(float $weightKg): float
    {
        return $weightKg * 1.0;
    }

    public function supports(string $carrier): bool
    {
        return $carrier === 'packgroup';
    }
}
