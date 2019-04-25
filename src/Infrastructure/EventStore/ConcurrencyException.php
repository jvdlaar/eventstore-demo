<?php

declare(strict_types=1);

namespace Infrastructure\EventStore;

use RuntimeException;

final class ConcurrencyException extends RuntimeException
{
    /**
     * @param int $expected
     * @param int $current
     *
     * @return static
     */
    public static function withVersions(int $expected, int $current): self
    {
        return new static(
            sprintf(
                'Expected version was %d, but event store reported the current version as %d',
                $expected,
                $current
            )
        );
    }
}
