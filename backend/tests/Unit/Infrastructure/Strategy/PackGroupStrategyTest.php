<?php

namespace App\Tests\Unit\Infrastructure\Strategy;

use App\Infrastructure\Strategy\PackGroupStrategy;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PackGroupStrategyTest extends TestCase
{
    #[DataProvider('weightProvider')]
    public function testCalculateUsesLinearPricing(float $weight, float $expected): void
    {
        $strategy = new PackGroupStrategy();

        self::assertSame($expected, $strategy->calculate($weight));
    }

    public static function weightProvider(): array
    {
        return [
            'integer' => [10.0, 10.0],
            'fraction' => [12.5, 12.5],
            'small' => [0.1, 0.1],
        ];
    }

    public function testSupportsOnlyPackGroupCarrier(): void
    {
        $strategy = new PackGroupStrategy();

        self::assertTrue($strategy->supports('packgroup'));
        self::assertFalse($strategy->supports('transcompany'));
    }
}
