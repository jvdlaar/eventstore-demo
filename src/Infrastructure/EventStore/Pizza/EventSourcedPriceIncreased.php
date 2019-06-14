<?php

declare(strict_types=1);

namespace Infrastructure\EventStore\Pizza;

use Domain\DTO\Price;
use Domain\Events\PriceIncreased;
use Domain\PizzaId;
use EventSauce\EventSourcing\Serialization\SerializableEvent;

final class EventSourcedPriceIncreased extends PriceIncreased implements SerializableEvent
{
    /**
     * @param PriceIncreased $priceSet
     *
     * @return EventSourcedPriceIncreased
     */
    public static function fromPriceSet(PriceIncreased $priceSet): self
    {
        return new static(
            $priceSet->pizzaId,
            $priceSet->price
        );
    }

    /**
     * @return array
     */
    public function toPayload(): array
    {
        return [
            'pizza_id' => $this->pizzaId->toString(),
            'price' => $this->price->getCents(),
        ];
    }

    /**
     * @param array $payload
     *
     * @return SerializableEvent
     */
    public static function fromPayload(array $payload): SerializableEvent
    {
        return new static(
            PizzaId::fromString(
                $payload['pizza_id']
            ),
            new Price(
                $payload['price']
            )
        );
    }
}
