<?php

declare(strict_types=1);

namespace Domain;

use RuntimeException;

final class PizzaNotYetCreated extends RuntimeException
{
    /**
     * @param PizzaId $id
     *
     * @return PizzaNotYetCreated
     */
    public static function with(PizzaId $id): self
    {
        return new static(
            sprintf('Pizza with id %s has not been created yet', (string) $id)
        );
    }
}
