<?php

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
}
