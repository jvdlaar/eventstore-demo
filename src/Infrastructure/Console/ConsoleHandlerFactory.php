<?php

declare(strict_types=1);

namespace Infrastructure\Console;

use League\Container\Container;

final class ConsoleHandlerFactory
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
     * @param string $consoleCommand
     * @param string $handlerClass
     */
    public function registerHandler(string $consoleCommand, string $handlerClass): void
    {
        $this->map[$consoleCommand] = $handlerClass;
    }

    /**
     * @param string $consoleCommand
     *
     * @return callable
     */
    public function createHandler(string $consoleCommand): callable
    {
        if (!isset($this->map[$consoleCommand])) {
            throw UnmappedCommand::withCommand($consoleCommand);
        }

        return $this->container->get(
            $this->map[$consoleCommand]
        );
    }

    /**
     * @return array
     */
    public function getCommands(): array
    {
        return array_keys($this->map);
    }
}
