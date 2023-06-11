<?php

declare(strict_types=1);


namespace Tests\EnjoysCMS\Traits;


use EnjoysCMS\Core\Auth\Identity;
use PHPUnit\Framework\MockObject\MockObject;

trait MockHelper
{
    /**
     * @template T
     * @param string|T $classString
     * @return MockObject&T
     */
    private function getMock(string $classString): MockObject
    {
        return $this->getMockBuilder($classString)->disableOriginalConstructor()->getMock();
    }
}
