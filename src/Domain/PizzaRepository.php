<?php

declare(strict_types=1);

namespace Domain;

interface PizzaRepository
{
    /**
     * @param PizzaId $id
     *
     * @return Pizza
     */
    public function retrieve(PizzaId $id): Pizza;

    /**
     * @param Pizza $pizza
     */
    public function persist(Pizza $pizza): void;
}
