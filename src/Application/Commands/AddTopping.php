<?php

declare(strict_types=1);

namespace Application\Commands;

use Domain\DTO\Topping;
use Domain\PizzaId;

final class AddTopping implements Command
{
    /**
     * @var PizzaId
     */
    private $pizzaId;

    /**
     * @var Topping
     */
    private $topping;

    /**
     * @param PizzaId $pizzaId
     * @param Topping $topping
     */
    public function __construct(PizzaId $pizzaId, Topping $topping)
    {
        $this->topping = $topping;
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
     * @return Topping
     */
    public function getTopping(): Topping
    {
        return $this->topping;
    }

}
