<?php

declare(strict_types=1);


namespace Tests\EnjoysCMS\Traits;


trait ReflectionTrait
{

    private function setProperty($object, $property, $value)
    {
        $reflectionProperty = new \ReflectionProperty($object, $property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $value);
    }
}
