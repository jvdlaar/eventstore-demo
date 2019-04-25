<?php

declare(strict_types=1);

namespace Infrastructure\CommandBus;

use Application\Commands\Command;
use Application\Commands\CommandBus as CommandBusInterface;

final class CommandBus implements CommandBusInterface
{
    /**
     * @var CommandHanderFactory
     */
    private $factory;

    /**
     * @param CommandHanderFactory $commandMap
     */
    public function __construct(CommandHanderFactory $commandMap)
    {
        $this->factory = $commandMap;
    }


    /**
     * @param Command $command
     */
    public function dispatch(Command $command): void
    {
        $handler = $this->factory->createHandler(
            \get_class($command)
        );

        $handler($command);
    }
}
