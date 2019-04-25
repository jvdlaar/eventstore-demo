<?php

declare(strict_types=1);

namespace Infrastructure\Console;

use Application\Commands\AddTopping;
use Application\Commands\CommandBus;
use Domain\DTO\Topping;
use Domain\PizzaId;

final class AddToppingCommand
{
    public const ADD_TOPPING = 'pizza:topping';

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


    /**
     * @param string $pizzaId
     * @param string $topping
     */
    public function __invoke(string $pizzaId, string $topping): void
    {
        $addTopping = new AddTopping(
            PizzaId::fromString(
                $pizzaId
            ),
            new Topping($topping)
        );
        $this->commandBus->dispatch($addTopping);

        echo "Pizza $pizzaId added $topping as topping\n";
    }
}
