<?php

declare(strict_types=1);

namespace Application\ReadModels;

use Domain\PizzaId;
use RuntimeException;

final class PizzaNotFound extends RuntimeException
{
    /**
     * @param PizzaId $pizzaId
     *
     * @return PizzaNotFound
     */
    public static function with(PizzaId $pizzaId): self
    {
        return new static(
            sprintf('Pizza with pizzaId %s is not found', (string) $pizzaId)
        );
    }
}
