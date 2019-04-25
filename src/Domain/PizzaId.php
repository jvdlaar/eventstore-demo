<?php

declare(strict_types=1);

namespace Domain;

final class PizzaId
{
    private $id;

    /**
     * @param string $id
     */
    private function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return PizzaId
     */
    public static function fromString(string $id): self
    {
        return new static($id);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id;
    }
}
