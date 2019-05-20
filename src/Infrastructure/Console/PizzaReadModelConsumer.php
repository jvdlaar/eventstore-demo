<?php

declare(strict_types=1);

namespace Infrastructure\Console;

use Amp\Loop;
use Generator;
use Infrastructure\EventStore\Pizza\PizzaReadModelProjection;
use Infrastructure\ReadModels\EventSourcedPizzaProjector;
use Prooph\EventStoreClient\EndPoint;
use Prooph\EventStoreClient\EventStoreConnectionFactory;

class PizzaReadModelConsumer
{
    public const READ_MODEL = 'pizza:read-model';


    /**
     * @var EndPoint
     */
    private $endPoint;

    /**
     * @var PizzaReadModelProjection
     */
    private $projection;

    /**
     * @var EventSourcedPizzaProjector
     */
    private $handler;

    /**
     * @param EndPoint                       $endPoint
     * @param PizzaReadModelProjection       $projection
     * @param EventSourcedPizzaProjector $handler
     */
    public function __construct(
        EndPoint $endPoint,
        PizzaReadModelProjection $projection,
        EventSourcedPizzaProjector $handler
    ) {
        $this->endPoint = $endPoint;
        $this->projection = $projection;
        $this->handler = $handler;
    }

    public function __invoke(): void
    {
        Loop::run(function(): Generator {
            $connection = EventStoreConnectionFactory::createFromEndPoint(
                $this->endPoint,
                null,
                uniqid('readmodel-', true)
            );
            yield $connection->connectAsync();

            yield $connection->connectToPersistentSubscriptionAsync(
                $this->projection->getStream(),
                $this->projection->getGroupName(),
                $this->handler
            );
        });
    }
}
