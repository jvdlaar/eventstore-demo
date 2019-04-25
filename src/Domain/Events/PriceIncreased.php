<?php

declare(strict_types=1);

namespace Domain\Events;

use Domain\DTO\Price;
use Domain\PizzaId;

class PriceIncreased implements Event
{
    /**
     * @var PizzaId
     */
    protected $pizzaId;

    /**
     * @var Price
     */
    protected $price;

    /**
     * @param PizzaId $pizzaId
     * @param Price   $price
     */
    public function __construct(PizzaId $pizzaId, Price $price)
    {
        $this->pizzaId = $pizzaId;
        $this->price = $price;
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
