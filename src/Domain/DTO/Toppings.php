<?php

declare(strict_types=1);

namespace Domain\DTO;

use ArrayIterator;

final class Toppings extends ArrayIterator
{
    /**
     * @param Topping[] $pizzas
     */
    public function __construct(Topping ...$pizzas)
    {
        parent::__construct($pizzas);
    }

    /**
     * @param Topping $topping
     *
     * @return Toppings
     */
    public function add(Topping $topping): self
    {
        $toppings = $this->getArrayCopy();
        $toppings[] = $topping;
        return new static(...array_values($toppings));
    }
}
