<?php

declare(strict_types=1);

namespace Infrastructure\Console;

use RuntimeException;

final class UnmappedCommand extends RuntimeException
{
    /**
     * @param string $command
     *
     * @return UnmappedCommand
     */
    public static function withCommand(string $command): self
    {
        return new static(
           sprintf('Command %s is not mapped', $command)
        );
    }
}
