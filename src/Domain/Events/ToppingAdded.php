<?php

declare(strict_types=1);

namespace Domain\Events;

use Domain\DTO\Topping;
use Domain\PizzaId;

class ToppingAdded implements Event
{
    /**
     * @var PizzaId
     */
    protected $pizzaId;

    /**
     * @var Topping
     */
    protected $topping;

    /**
     * @param PizzaId $pizzaId
     * @param Topping $topping
     */
    public function __construct(PizzaId $pizzaId, Topping $topping)
    {
        $this->pizzaId = $pizzaId;
        $this->topping = $topping;
    }

    /**
     * @return PizzaId
     */
    public function getPizzaId(): PizzaId
    {
        return $this->pizzaId;
    }

    /**
     * @return Topping
     */
    public function getTopping(): Topping
    {
        return $this->topping;
    }
}
