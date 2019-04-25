<?php

declare(strict_types=1);

namespace Domain\DTO;

final class Price
{
    /**
     * @var int
     */
    private $cents;

    /**
     * @param int $cents
     */
    public function __construct(int $cents)
    {
        $this->cents = $cents;
    }

    /**
     * @return int
     */
    public function getCents(): int
    {
        return $this->cents;
    }
}
