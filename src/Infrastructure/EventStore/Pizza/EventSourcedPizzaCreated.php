<?php

declare(strict_types=1);

namespace Infrastructure\EventStore\Pizza;

use Domain\DTO\PizzaName;
use Domain\Events\PizzaCreated;
use Domain\PizzaId;
use EventSauce\EventSourcing\Serialization\SerializableEvent;

final class EventSourcedPizzaCreated extends PizzaCreated implements SerializableEvent
{
    /**
     * @param PizzaCreated $pizzaCreated
     *
     * @return EventSourcedPizzaCreated
     */
    public static function fromPizzaCreated(PizzaCreated $pizzaCreated): self
    {
        return new static(
            $pizzaCreated->pizzaId,
            $pizzaCreated->name
        );
    }

    /** @inheritdoc */
    public function toPayload(): array
    {
        return [
            'pizza_id' => $this->pizzaId->toString(),
            'name' => $this->name->getName()
        ];
    }

    /** @inheritdoc */
    public static function fromPayload(array $payload): SerializableEvent
    {
        return new static(
            PizzaId::fromString($payload['pizza_id']),
            new PizzaName($payload['name'])
        );
    }
}
