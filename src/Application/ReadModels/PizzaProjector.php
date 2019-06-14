<?php

declare(strict_types=1);

namespace Application\ReadModels;

use Domain\Events\Event;
use Domain\Events\PizzaCreated;
use Domain\Events\PriceIncreased;
use Domain\Events\ToppingAdded;
use function get_class;
use RuntimeException;

final class PizzaProjector
{
    /**
     * @var PizzaReadModel
     */
    private $readModel;

    /**
     * @param PizzaReadModel $readModel
     */
    public function __construct(PizzaReadModel $readModel)
    {
        $this->readModel = $readModel;
    }

    /**
     * @param Event $event
     */
    public function __invoke(Event $event): void
    {
        switch (true) {
            case is_a($event, PizzaCreated::class):
                /** @var PizzaCreated $event */
                $this->readModel->createPizza(
                    $event->getPizzaId(),
                    $event->getName()
                );
                break;

            case is_a($event, ToppingAdded::class):
                /** @var ToppingAdded $event */
                $this->readModel->addTopping(
                    $event->getPizzaId(),
                    $event->getTopping()
                );
                break;

            case is_a($event, PriceIncreased::class):
                /** @var PriceIncreased $event */
                $this->readModel->increasePrice(
                    $event->getPizzaId(),
                    $event->getPrice()
                );
                break;

            default:
                throw new RuntimeException('Unknown event type');
        }
    }
}
