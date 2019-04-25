<?php

declare(strict_types=1);

namespace Infrastructure\EventStore\Pizza;

use Domain\Events\Event;
use Domain\Events\PizzaCreated;
use Domain\Events\PriceIncreased;
use Domain\Events\ToppingAdded;
use Domain\Pizza;
use Domain\PizzaId;
use EventSauce\EventSourcing\AggregateRoot;
use EventSauce\EventSourcing\AggregateRootBehaviour\ConstructionBehaviour;
use EventSauce\EventSourcing\AggregateRootBehaviour\EventRecordingBehaviour;
use Infrastructure\EventStore\UnmappedEvent;

final class EventSourcedPizza extends Pizza implements AggregateRoot
{
    use ConstructionBehaviour,
        EventRecordingBehaviour;

    /**
     * @return PizzaId
     */
    protected function id(): PizzaId
    {
        $id = $this->aggregateRootId();
        return PizzaId::fromString(
            $id->toString()
        );
    }

    protected function apply(object $event)
    {
        /** @var Event $event */
        $this->applyEvent($event);
    }

    /**
     * @param Event $event
     */
    protected function recordEvent(Event $event): void
    {
        switch (true) {
            case $event instanceof PizzaCreated:
                $this->recordThat(
                    EventSourcedPizzaCreated::fromPizzaCreated($event)
                );
                break;

            case $event instanceof PriceIncreased:
                $this->recordThat(
                    EventSourcedPriceIncreased::fromPriceSet($event)
                );
                break;

            case $event instanceof ToppingAdded:
                $this->recordThat(
                    EventSourcedToppingAdded::fromToppingAdded($event)
                );
                break;

            default:
                throw UnmappedEvent::withEvent(get_class($event));
        }
    }
}
