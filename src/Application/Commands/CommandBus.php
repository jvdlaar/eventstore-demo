<?php

declare(strict_types=1);

namespace Application\Commands;

interface CommandBus
{
    /**
     * @param Command $command
     */
    public function dispatch(Command $command): void;
}
