<?php

declare(strict_types=1);

namespace Infrastructure\CommandBus;

use League\Container\Container;

final class CommandHanderFactory
{
    /**
     * @var array
     */
    private $map = [];

    /**
     * @var Container
     */
    private $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $commandClass
     * @param string $handlerClass
     */
    public function registerHandler(string $commandClass, string $handlerClass): void
    {
        $this->map[$commandClass] = $handlerClass;
    }

    /**
     * @param string $commandClass
     *
     * @return callable
     */
    public function createHandler(string $commandClass): callable
    {
        if (!isset($this->map[$commandClass])) {
            throw UnmappedCommand::withCommand($commandClass);
        }

        return $this->container->get(
            $this->map[$commandClass]
        );
    }
}
