<?php

declare(strict_types=1);

namespace Application\Commands;

use Domain\PizzaRepository;

final class AddToppingHandler
{
    /** @var PizzaRepository  */
    private $pizzaRepository;

    /**
     * @param $pizzaRepository
     */
    public function __construct(PizzaRepository $pizzaRepository)
    {
        $this->pizzaRepository = $pizzaRepository;
    }

    /**
     * @param AddTopping $command
     */
    public function __invoke(AddTopping $command): void
    {
        $pizza = $this->pizzaRepository->retrieve($command->getPizzaId());
        $pizza->addTopping($command->getTopping());
        $this->pizzaRepository->persist($pizza);
    }
}
