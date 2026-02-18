<?php

namespace App\Infrastructure\Strategy;

use App\Domain\ShippingStrategyInterface;

class TransCompanyStrategy implements ShippingStrategyInterface
{
    public function calculate(float $weightKg): float
    {
        if ($weightKg <= 10) {
            return 20.0;
        }

        return 100.0;
    }

    public function supports(string $carrier): bool
    {
        return $carrier === 'transcompany';
    }
}
