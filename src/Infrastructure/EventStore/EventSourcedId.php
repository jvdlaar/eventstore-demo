<?php

declare(strict_types=1);

namespace Infrastructure\EventStore;

use EventSauce\EventSourcing\AggregateRootId;

final class EventSourcedId implements AggregateRootId
{
    /**
     * @var string
     */
    private $id;

    /**
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }


    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->id;
    }

    /**
     * @param string $aggregateRootId
     *
     * @return static
     */
    public static function fromString(string $aggregateRootId): AggregateRootId
    {
        return new self($aggregateRootId);
    }
}
