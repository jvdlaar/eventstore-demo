<?php

declare(strict_types=1);

namespace Application\Commands;

use Domain\DTO\Price;
use Domain\PizzaId;

final class IncreasePrice implements Command
{
    /**
     * @var PizzaId
     */
    private $pizzaId;

    /**
     * @var Price
     */
    private $price;

    /**
     * @param PizzaId $pizzaId
     * @param Price   $price
     */
    public function __construct(PizzaId $pizzaId, Price $price)
    {
        $this->price = $price;
        $this->pizzaId = $pizzaId;
    }

    /**
     * @return PizzaId
     */
    public function getPizzaId(): PizzaId
    {
        return $this->pizzaId;
    }

    /**
     * @return Price
     */
    public function getPrice(): Price
    {
        return $this->price;
    }

}
