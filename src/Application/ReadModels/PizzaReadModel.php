<?php

declare(strict_types=1);

namespace Application\ReadModels;

use Domain\DTO\PizzaName;
use Domain\DTO\Price;
use Domain\DTO\Topping;
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

    /**
     * @param PizzaId   $pizzaId
     * @param PizzaName $name
     */
    public function createPizza(PizzaId $pizzaId, PizzaName $name): void;

    /**
     * @param PizzaId $pizzaId
     * @param Price   $price
     */
    public function increasePrice(PizzaId $pizzaId, Price $price): void;

    /**
     * @param PizzaId $pizzaId
     * @param Topping $topping
     */
    public function addTopping(PizzaId $pizzaId, Topping $topping): void;
}
