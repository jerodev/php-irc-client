<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use ReflectionClass;

class TestCase extends PHPUnitTestCase
{
    protected function callPrivate($object, string $method, array $args)
    {
        $reflector = new ReflectionClass(get_class($object));
        $method = $reflector->getMethod($method);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $args);
    }

    protected function setPrivate($object, string $property, $value = null): void
    {
        $reflector = new ReflectionClass(get_class($object));
        $property = $reflector->getProperty($property);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }

    protected function getPrivate($object, string $property)
    {
        $reflector = new ReflectionClass(get_class($object));
        $property = $reflector->getProperty($property);
        $property->setAccessible(true);

        return $property->getValue($object);
    }
}
