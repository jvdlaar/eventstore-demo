<?php

declare(strict_types=1);

namespace Infrastructure\EventStore\Pizza;

use Domain\Pizza;
use Domain\PizzaId;
use Domain\PizzaRepository;
use EventSauce\EventSourcing\AggregateRootRepository;

final class EventSourcedPizzaRepository implements PizzaRepository
{
    /**
     * @var AggregateRootRepository
     */
    private $aggregateRootRepository;

    /**
     * @param AggregateRootRepository $aggregateRootRepository
     */
    public function __construct(AggregateRootRepository $aggregateRootRepository)
    {
        $this->aggregateRootRepository = $aggregateRootRepository;
    }

    /** @inheritdoc */
    public function retrieve(PizzaId $id): Pizza
    {
        /** @var Pizza $pizza */
        $pizza = $this->aggregateRootRepository->retrieve(
            new EventSourcedPizzaId(
                $id->toString()
            )
        );
        return $pizza;
    }

    /**
     * @param Pizza $pizza
     */
    public function persist(Pizza $pizza): void
    {
        $this->aggregateRootRepository->persist($pizza);
    }
}
