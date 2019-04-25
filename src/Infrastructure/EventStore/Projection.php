<?php

declare(strict_types=1);

namespace Infrastructure\EventStore;

interface Projection
{

    /**
     * @return string
     */
    public function getGroupName(): string;

    /**
     * @return string
     */
    public function getQuery(): string;

    /**
     * @return string
     */
    public function getStream(): string;
}
