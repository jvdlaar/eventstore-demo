<?php

declare(strict_types=1);

namespace Infrastructure\EventStore;

use RuntimeException;

final class UnmappedEvent extends RuntimeException
{
    /**
     * @param string $event
     *
     * @return UnmappedEvent
     */
    public static function withEvent(string $event): self
    {
        return new static(
           sprintf('Event %s is not mapped', $event)
        );
    }
}
