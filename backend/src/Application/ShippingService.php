<?php

namespace App\Application;

use App\Domain\Model\ShippingRequest;
use App\Domain\ShippingStrategyInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class ShippingService
{
    private iterable $strategies;
    private LoggerInterface $logger;

    public function __construct(
        #[TaggedIterator('app.shipping_strategy')] iterable $strategies,
        LoggerInterface $logger
    ) {
        $this->strategies = $strategies;
        $this->logger = $logger;
    }

    public function calculate(ShippingRequest $request): float
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy instanceof ShippingStrategyInterface && $strategy->supports($request->carrier)) {
                $cost = $strategy->calculate($request->weightKg);

                $this->logger->info('Shipping calculated', [
                    'carrier' => $request->carrier,
                    'weight' => $request->weightKg,
                    'cost' => $cost
                ]);

                return $cost;
            }
        }

        $this->logger->error('Unsupported carrier requested', [
            'carrier' => $request->carrier
        ]);

        throw new \InvalidArgumentException(sprintf('Carrier "%s" is not supported.', $request->carrier));
    }
}
