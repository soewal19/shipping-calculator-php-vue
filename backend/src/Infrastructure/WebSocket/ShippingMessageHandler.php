<?php

namespace App\Infrastructure\WebSocket;

use App\Application\ShippingService;
use App\Domain\Model\ShippingRequest;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class ShippingMessageHandler implements MessageComponentInterface
{
    private ShippingService $shippingService;

    public function __construct(ShippingService $shippingService)
    {
        $this->shippingService = $shippingService;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Connection opened
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);

        if (!isset($data['carrier']) || !isset($data['weightKg'])) {
            $from->send(json_encode(['error' => 'Invalid data']));
            return;
        }

        try {
            $request = new ShippingRequest($data['carrier'], (float) $data['weightKg']);
            $cost = $this->shippingService->calculate($request);
            $from->send(json_encode(['cost' => $cost]));
        } catch (\Exception $e) {
            $from->send(json_encode(['error' => $e->getMessage()]));
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // Connection closed
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();
    }
}
