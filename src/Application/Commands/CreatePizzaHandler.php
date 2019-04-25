<?php

declare(strict_types=1);

namespace Application\Commands;

use Domain\PizzaRepository;

final class CreatePizzaHandler
{
    /** @var PizzaRepository */
    private $pizzaRepository;

    /**
     * @param $pizzaRepository
     */
    public function __construct(PizzaRepository $pizzaRepository)
    {
        $this->pizzaRepository = $pizzaRepository;
    }

    /**
     * @param CreatePizza $command
     */
    public function __invoke(CreatePizza $command): void
    {
        $pizza = $this->pizzaRepository->retrieve(
            $command->getPizzaId()
        );
        $pizza->createPizza($command->getName());
        $this->pizzaRepository->persist($pizza);
    }
}
