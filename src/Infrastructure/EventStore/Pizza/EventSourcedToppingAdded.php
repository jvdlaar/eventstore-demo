<?php

declare(strict_types=1);

namespace Infrastructure\EventStore\Pizza;

use Domain\DTO\Topping;
use Domain\Events\ToppingAdded;
use Domain\PizzaId;
use EventSauce\EventSourcing\Serialization\SerializableEvent;

final class EventSourcedToppingAdded extends ToppingAdded implements SerializableEvent
{
    /**
     * @param ToppingAdded $toppingAdded
     *
     * @return EventSourcedToppingAdded
     */
    public static function fromToppingAdded(ToppingAdded $toppingAdded): self
    {
        return new static(
            $toppingAdded->pizzaId,
            $toppingAdded->topping
        );
    }

    /**
     * @return array
     */
    public function toPayload(): array
    {
        return [
            'pizza_id' => $this->pizzaId->toString(),
            'topping' => $this->topping->getName()
        ];
    }

    /**
     * @param array $payload
     *
     * @return SerializableEvent
     */
    public static function fromPayload(array $payload): SerializableEvent
    {
        return new self(
            PizzaId::fromString($payload['pizza_id']),
            new Topping($payload['topping'])
        );
    }
}
