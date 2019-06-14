<?php

declare(strict_types=1);

namespace Infrastructure\ReadModels;

use Amp\Promise;
use Amp\Success;
use Application\ReadModels\PizzaProjector;
use EventSauce\EventSourcing\Serialization\MessageSerializer;
use Prooph\EventStoreClient\EventAppearedOnCatchupSubscription;
use Prooph\EventStoreClient\Internal\EventStoreCatchUpSubscription;
use Prooph\EventStoreClient\ResolvedEvent;

final class EventSourcedPizzaProjector implements EventAppearedOnCatchupSubscription
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

    public function __invoke(EventStoreCatchUpSubscription $subscription, ResolvedEvent $resolvedEvent): Promise
    {
        $payload = [
            'headers' => json_decode($resolvedEvent->event()->metadata(), true),
            'payload' => json_decode($resolvedEvent->event()->data(), true),
        ];

        foreach ($this->serializer->unserializePayload($payload) as $event) {
            ($this->projector)($event->event());
        }

        return new Success();
    }
}
