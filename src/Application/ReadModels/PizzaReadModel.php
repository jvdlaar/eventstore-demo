<?php

declare(strict_types=1);

namespace Application\ReadModels;

use Domain\PizzaId;

interface PizzaReadModel
{
    /**
     * @param PizzaId $pizzaId
     *
     * @return Pizza
     */
    public function find(PizzaId $pizzaId): Pizza;

    /**
     * @return Pizza[]
     */
    public function all(): iterable;
}
