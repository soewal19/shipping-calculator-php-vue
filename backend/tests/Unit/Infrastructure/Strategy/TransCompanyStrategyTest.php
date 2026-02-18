<?php

namespace App\Tests\Unit\Infrastructure\Strategy;

use App\Infrastructure\Strategy\TransCompanyStrategy;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TransCompanyStrategyTest extends TestCase
{
    #[DataProvider('weightProvider')]
    public function testCalculateFollowsBusinessRules(float $weight, float $expected): void
    {
        $strategy = new TransCompanyStrategy();

        self::assertSame($expected, $strategy->calculate($weight));
    }

    public static function weightProvider(): array
    {
        return [
            'below threshold' => [9.9, 20.0],
            'equal threshold' => [10.0, 20.0],
            'above threshold' => [10.1, 100.0],
            'much above threshold' => [42.0, 100.0],
        ];
    }

    public function testSupportsOnlyTransCompanyCarrier(): void
    {
        $strategy = new TransCompanyStrategy();

        self::assertTrue($strategy->supports('transcompany'));
        self::assertFalse($strategy->supports('packgroup'));
    }
}
