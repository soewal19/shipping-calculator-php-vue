<?php

namespace App\Infrastructure\WebSocket;

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Application\ShippingService;

#[AsCommand(name: 'run:websocket')]
class RunWebSocketServerCommand extends Command
{
    private ShippingService $shippingService;

    public function __construct(ShippingService $shippingService)
    {
        $this->shippingService = $shippingService;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Starting WebSocket server on port 8081...');

        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new ShippingMessageHandler($this->shippingService)
                )
            ),
            8081
        );

        $server->run();

        return Command::SUCCESS;
    }
}
