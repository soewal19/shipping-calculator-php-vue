<?php

namespace App\Domain\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ShippingRequest
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    public string $carrier;

    #[Assert\NotBlank]
    #[Assert\Type('numeric')]
    #[Assert\Positive]
    public float $weightKg;

    public function __construct(string $carrier, float $weightKg)
    {
        $this->carrier = $carrier;
        $this->weightKg = $weightKg;
    }
}
