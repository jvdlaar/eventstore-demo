<?php

declare(strict_types=1);

namespace Application\Commands;

use Domain\PizzaRepository;

final class IncreasePriceHandler
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
     * @param IncreasePrice $command
     */
    public function __invoke(IncreasePrice $command): void
    {
        $pizza = $this->pizzaRepository->retrieve($command->getPizzaId());
        $price = $command->getPrice();
        $pizza->increasePrice(
            $price
        );
        $this->pizzaRepository->persist($pizza);
    }
}
