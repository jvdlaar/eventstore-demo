<?php

declare(strict_types=1);

namespace Infrastructure\Console;

use Amp\Promise;
use Amp\Success;
use Application\Commands\IncreasePrice;
use Domain\DTO\Price;
use Domain\PizzaId;
use Infrastructure\CommandBus\CommandBus;
use Prooph\EventStoreClient\EventAppearedOnPersistentSubscription;
use Prooph\EventStoreClient\Internal\EventStorePersistentSubscription;
use Prooph\EventStoreClient\ResolvedEvent;

final class PriceCalculationConsumeHandler implements EventAppearedOnPersistentSubscription
{
    /**
     * @var CommandBus
     */
    private $commandBus;


    /**
     * @param CommandBus $commandBus
     */
    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function __invoke(EventStorePersistentSubscription $subscription, ResolvedEvent $resolvedEvent, ?int $retryCount = null): Promise
    {
        echo 'incoming event: ' . $resolvedEvent->originalEventNumber() . '@' . $resolvedEvent->originalStreamName() . PHP_EOL;

        $data = json_decode($resolvedEvent->event()->data(), true);

        $pizzaId = PizzaId::fromString(
            $data['pizza_id']
        );

        $price = new Price(\strlen($data['name']));

        $this->commandBus->dispatch(new IncreasePrice($pizzaId, $price));

        return new Success();
    }
}
