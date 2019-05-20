<?php

declare(strict_types=1);

namespace Infrastructure\ReadModels;

use Amp\Promise;
use Amp\Success;
use Application\ReadModels\PizzaProjector;
use EventSauce\EventSourcing\Serialization\MessageSerializer;
use Prooph\EventStoreClient\EventAppearedOnPersistentSubscription;
use Prooph\EventStoreClient\Internal\EventStorePersistentSubscription;
use Prooph\EventStoreClient\ResolvedEvent;

final class EventSourcedPizzaProjector implements EventAppearedOnPersistentSubscription
{
    /**
     * @var PizzaProjector
     */
    private $projector;

    /**
     * @var MessageSerializer
     */
    private $serializer;

    /**
     * @param PizzaProjector    $projector
     * @param MessageSerializer $serializer
     */
    public function __construct(PizzaProjector $projector, MessageSerializer $serializer)
    {
        $this->projector = $projector;
        $this->serializer = $serializer;
    }

    public function __invoke(EventStorePersistentSubscription $subscription, ResolvedEvent $resolvedEvent, ?int $retryCount = null): Promise
    {
        $payload = [
            'headers' => json_decode($resolvedEvent->event()->metadata(), true),
            'payload' => json_decode($resolvedEvent->event()->data(), true),
        ];

        foreach ($this->serializer->unserializePayload($payload) as $event) {
            ($this->projector)($event);
        }

        return new Success();
    }
}
