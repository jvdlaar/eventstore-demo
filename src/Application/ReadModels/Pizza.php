<?php

declare(strict_types=1);

namespace Application\ReadModels;

use Domain\DTO\PizzaName;
use Domain\DTO\Price;
use Domain\DTO\Topping;
use Domain\DTO\Toppings;
use Domain\PizzaId;

class Pizza
{
    /**
     * @var PizzaId
     */
    private $pizzaId;

    /**
     * @var PizzaName
     */
    private $pizzaName;

    /**
     * @var Price
     */
    private $price;

    /**
     * @var Toppings
     */
    private $toppings;

    /**
     * @param PizzaId   $pizzaId
     * @param PizzaName $pizzaName
     * @param Price     $price
     * @param Toppings  $toppings
     */
    public function __construct(
        PizzaId $pizzaId,
        PizzaName $pizzaName,
        Price $price,
        Toppings $toppings
    )
    {
        $this->pizzaId = $pizzaId;
        $this->pizzaName = $pizzaName;
        $this->price = $price;
        $this->toppings = $toppings;
    }

    /**
     * @return PizzaId
     */
    public function getPizzaId(): PizzaId
    {
        return $this->pizzaId;
    }

    /**
     * @return PizzaName
     */
    public function getPizzaName(): PizzaName
    {
        return $this->pizzaName;
    }

    /**
     * @return Price
     */
    public function getPrice(): Price
    {
        return $this->price;
    }

    /**
     * @return Toppings
     */
    public function getToppings(): Toppings
    {
        return $this->toppings;
    }

    /**
     * @return string[]
     */
    public function toArray(): array
    {
        $toppings = [];

        /** @var Topping $topping */
        foreach ($this->toppings as $topping) {
            $toppings[] = $topping->getName();
        }
        return $toppings;
    }

}
