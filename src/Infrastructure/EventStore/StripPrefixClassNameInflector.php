<?php

declare(strict_types=1);

namespace Infrastructure\EventStore;

use EventSauce\EventSourcing\ClassNameInflector as BaseClassNameInflector;

final class StripPrefixClassNameInflector implements BaseClassNameInflector
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @param string $prefix
     */
    public function __construct(string $prefix)
    {
        $this->prefix = $prefix;
    }

    /** @inheritdoc */
    public function classNameToType(string $className): string
    {
        return str_replace($this->prefix, '', $className);
    }

    /** @inheritdoc */
    public function typeToClassName(string $eventName): string
    {
        return $this->prefix . $eventName;
    }

    /** @inheritdoc */
    public function instanceToType(object $instance): string
    {
        return $this->classNameToType(get_class($instance));
    }
}
