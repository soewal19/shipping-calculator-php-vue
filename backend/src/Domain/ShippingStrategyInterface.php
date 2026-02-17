<?php

namespace App\Domain;

interface ShippingStrategyInterface
{
    /**
     * Calculates the shipping cost based on the weight.
     *
     * @param float $weightKg
     * @return float
     */
    public function calculate(float $weightKg): float;

    /**
     * Checks if this strategy supports the given carrier name.
     *
     * @param string $carrier
     * @return bool
     */
    public function supports(string $carrier): bool;
}
