<?php

declare(strict_types=1);

namespace Application\Commands;

use Domain\DTO\PizzaName;
use Domain\PizzaId;

final class CreatePizza implements Command
{
    /**
     * @var PizzaId
     */
    private $pizzaId;

    /**
     * @var PizzaName
     */
    private $name;

    /**
     * @param PizzaId   $pizzaId
     * @param PizzaName $name
     */
    public function __construct(PizzaId $pizzaId, PizzaName $name)
    {
        $this->pizzaId = $pizzaId;
        $this->name = $name;
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
    public function getName(): PizzaName
    {
        return $this->name;
    }

}
