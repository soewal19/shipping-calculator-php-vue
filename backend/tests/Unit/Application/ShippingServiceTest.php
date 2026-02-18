<?php

namespace App\Tests\Unit\Application;

use App\Application\ShippingService;
use App\Domain\Model\ShippingRequest;
use App\Domain\ShippingStrategyInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ShippingServiceTest extends TestCase
{
    public function testCalculateUsesMatchingStrategyAndLogsInfo(): void
    {
        $request = new ShippingRequest('packgroup', 12.5);

        $strategyA = new class () implements ShippingStrategyInterface {
            public function calculate(float $weightKg): float
            {
                return 0.0;
            }

            public function supports(string $carrier): bool
            {
                return $carrier === 'transcompany';
            }
        };

        $strategyB = new class () implements ShippingStrategyInterface {
            public function calculate(float $weightKg): float
            {
                return $weightKg;
            }

            public function supports(string $carrier): bool
            {
                return $carrier === 'packgroup';
            }
        };

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())
            ->method('info')
            ->with(
                'Shipping calculated',
                [
                    'carrier' => 'packgroup',
                    'weight' => 12.5,
                    'cost' => 12.5,
                ]
            );
        $logger->expects(self::never())->method('error');

        $service = new ShippingService([$strategyA, $strategyB], $logger);

        self::assertSame(12.5, $service->calculate($request));
    }

    public function testCalculateThrowsExceptionForUnsupportedCarrierAndLogsError(): void
    {
        $request = new ShippingRequest('unknown-carrier', 5.0);

        $strategy = new class () implements ShippingStrategyInterface {
            public function calculate(float $weightKg): float
            {
                return 10.0;
            }

            public function supports(string $carrier): bool
            {
                return false;
            }
        };

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())
            ->method('error')
            ->with('Unsupported carrier requested', ['carrier' => 'unknown-carrier']);
        $logger->expects(self::never())->method('info');

        $service = new ShippingService([$strategy], $logger);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Carrier "unknown-carrier" is not supported.');

        $service->calculate($request);
    }
}
