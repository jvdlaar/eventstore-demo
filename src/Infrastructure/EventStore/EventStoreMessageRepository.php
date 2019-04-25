<?php

declare(strict_types=1);

namespace Infrastructure\EventStore;

use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageRepository;
use Generator;
use Ramsey\Uuid\Uuid;

final class EventStoreMessageRepository implements MessageRepository
{
    /**
     * @var EventsReader
     */
    private $eventsReader;

    /**
     * @var EventWriter
     */
    private $eventWriter;

    /**
     * @var string
     */
    private $streamPrefix;

    /**
     * @param EventsReader $eventsReader
     * @param EventWriter  $eventWriter
     * @param string       $streamPrefix
     */
    public function __construct(
        EventsReader $eventsReader,
        EventWriter $eventWriter,
        string $streamPrefix
    ) {
        $this->eventsReader = $eventsReader;
        $this->eventWriter = $eventWriter;
        $this->streamPrefix = $streamPrefix;
    }

    /**
     * @param Message ...$messages
     */
    public function persist(Message ...$messages): void
    {
        foreach ($messages as $message) {
            ($this->eventWriter)(
                $message,
                $this->createStreamName($message->aggregateRootId())
            );
        }
    }

    /**
     * @param AggregateRootId $id
     *
     * @return Generator
     */
    public function retrieveAll(AggregateRootId $id): Generator
    {
        yield from ($this->eventsReader)(
            $this->createStreamName($id)
        );
    }

    /**
     * @param AggregateRootId $id
     *
     * @return string
     */
    private function createStreamName(AggregateRootId $id): string
    {
        return $this->streamPrefix . '-' . Uuid::fromString($id->toString())->getHex();
    }
}
