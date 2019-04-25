<?php

declare(strict_types=1);

namespace Domain\Events;

use Domain\DTO\PizzaName;
use Domain\PizzaId;

class PizzaCreated implements Event
{
    /**
     * @var PizzaId
     */
    protected $pizzaId;

    /**
     * @var PizzaName
     */
    protected $name;

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
