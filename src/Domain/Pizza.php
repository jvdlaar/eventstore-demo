<?php

declare(strict_types=1);

namespace Domain;

use Domain\DTO\PizzaName;
use Domain\DTO\Price;
use Domain\DTO\Topping;
use Domain\DTO\Toppings;
use Domain\Events\Event;
use Domain\Events\PizzaCreated;
use Domain\Events\PriceIncreased;
use Domain\Events\ToppingAdded;

abstract class Pizza
{
    /** @var PizzaName */
    private $name;

    /** @var Price */
    private $price;

    /** @var Toppings */
    private $toppings;

    /**
     * @return PizzaId
     */
    abstract protected function id(): PizzaId;

    /**
     * @param Event $event
     */
    abstract protected function recordEvent(Event $event): void;

    /**
     * @param PizzaName $name
     */
    public function createPizza(PizzaName $name): void
    {
        $this->recordEvent(
            new PizzaCreated($this->id(), $name)
        );
    }

    /**
     * @param Topping $topping
     */
    public function addTopping(Topping $topping): void
    {
        $this->assertCreated();

        $this->recordEvent(
            new ToppingAdded($this->id(), $topping)
        );
    }

    /**
     * @param Price $price
     */
    public function increasePrice(Price $price): void
    {
        $this->assertCreated();

        $this->recordEvent(
            new PriceIncreased($this->id(), $price)
        );
    }

    /**
     * @param Event $event
     */
    protected function applyEvent(Event $event): void
    {
        if ($event instanceof PizzaCreated) {
            $this->name = $event->getName();
        }

        if ($event instanceof PriceIncreased) {
            $this->price = $event->getPrice();
        }

        if ($event instanceof ToppingAdded) {
            if ($this->toppings === null) {
                $this->toppings = new Toppings($event->getTopping());
            }
            else {
                $this->toppings = $this->toppings->add($event->getTopping());
            }
        }
    }

    private function assertCreated(): void
    {
        if ($this->name === null) {
            throw PizzaNotYetCreated::with($this->id());
        }
    }
}
