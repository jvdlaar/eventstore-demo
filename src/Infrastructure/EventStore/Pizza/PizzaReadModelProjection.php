<?php

declare(strict_types=1);

namespace Infrastructure\EventStore\Pizza;

use Infrastructure\EventStore\Projection;

final class PizzaReadModelProjection implements Projection
{
    private const QUERY = <<<'QUERY'
        fromCategory('PizzaV1')
            .when({
                PizzaCreated: function(state, event) {
                    linkTo('PizzaReadModelV1', event);
                },
                PriceIncreased: function(state, event) {
                    linkTo('PizzaReadModelV1', event);
                },
                ToppingAdded: function(state, event) {
                    linkTo('PizzaReadModelV1', event);
                }
            })
        ;
QUERY;

    private const STREAM = 'PizzaReadModelV1';
    private const GROUP_NAME = 'main';

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return self::QUERY;
    }

    /**
     * @return string
     */
    public function getStream(): string
    {
        return self::STREAM;
    }

    /**
     * @return string
     */
    public function getGroupName(): string
    {
        return self::GROUP_NAME;
    }

    /**
     * @return bool
     */
    public function isPersistentSubscription(): bool
    {
        return false;
    }
}
