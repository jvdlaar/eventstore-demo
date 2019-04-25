<?php

declare(strict_types=1);

namespace Infrastructure\Console;

use Application\Commands\CommandBus;
use Application\Commands\CreatePizza;
use Domain\DTO\PizzaName;
use Domain\PizzaId;
use Ramsey\Uuid\Uuid;

final class CreatePizzaCommand
{
    public const CREATE_PIZZA = 'pizza:create';

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
     * @param string $name
     */
    public function __invoke(string $name): void
    {
        $pizzaId = PizzaId::fromString(
            (string) Uuid::uuid4()
        );
        $createPizza = new CreatePizza(
            $pizzaId,
            new PizzaName($name)
        );
        $this->commandBus->dispatch($createPizza);

        echo "Pizza $name created with id $pizzaId\n";
    }
}
