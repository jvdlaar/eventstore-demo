<?php

declare(strict_types=1);

namespace Infrastructure\Console;

use Amp\Loop;
use Generator;
use Infrastructure\EventStore\Pizza\PriceCalculationProjection;
use Prooph\EventStoreClient\EndPoint;
use Prooph\EventStoreClient\EventStoreConnectionFactory;

final class PriceCalculationConsumer
{
    public const CALCULATE_PRICE = 'pizza:price';

    /**
     * @var EndPoint
     */
    private $endPoint;

    /**
     * @var PriceCalculationProjection
     */
    private $projection;

    /**
     * @var PriceCalculationConsumeHandler
     */
    private $handler;

    /**
     * @param EndPoint                       $endPoint
     * @param PriceCalculationProjection     $projection
     * @param PriceCalculationConsumeHandler $handler
     */
    public function __construct(
        EndPoint $endPoint,
        PriceCalculationProjection $projection,
        PriceCalculationConsumeHandler $handler
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
                uniqid('price-', true)
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
