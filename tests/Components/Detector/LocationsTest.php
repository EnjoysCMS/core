<?php

declare(strict_types=1);

namespace Tests\EnjoysCMS\Components\Detector;

use Doctrine\ORM\EntityManager;
use EnjoysCMS\Core\Entities\Location;
use EnjoysCMS\Core\Repositories\Locations;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Route;
use Tests\EnjoysCMS\Traits\MockHelper;

class LocationsTest extends TestCase
{
    use MockHelper;

    public function testSetCurrentLocationIfExist()
    {
        $locationEntity = new Location();
        $locationEntity->setName('test-name');
        $locationEntity->setLocation('test-location');

        $em = $this->getMock(EntityManager::class);
        $locationsRepository = $this->getMock(Locations::class);
        $locationsRepository->method('findOneBy')->willReturn($locationEntity);
        $em->method('getRepository')->willReturn($locationsRepository);

        $locations = new \EnjoysCMS\Core\Components\Detector\Locations($em);
        $locations->setCurrentLocation(new Route('test-route'));
        $this->assertSame('test-name', $locations::getCurrentLocation()->getName());
        $this->assertSame('test-location', $locations::getCurrentLocation()->getLocation());
    }

    public function testSetCurrentLocationIfNotExistAndNotHaveTitle()
    {
        $locationEntity = new Location();
        $locationEntity->setName('test-name');
        $locationEntity->setLocation('test-location');

        $em = $this->getMock(EntityManager::class);
        $locationsRepository = $this->getMock(Locations::class);
        $locationsRepository->method('findOneBy')->willReturn(null);
        $em->method('getRepository')->willReturn($locationsRepository);

        $locations = new \EnjoysCMS\Core\Components\Detector\Locations($em);

        $route = new Route('test-route');
        $route->setDefault('_controller', ['App\Namespace', 'method']);
        $locations->setCurrentLocation($route);
        $this->assertSame('App\Namespace::method', $locations::getCurrentLocation()->getName());
        $this->assertSame('App\Namespace::method', $locations::getCurrentLocation()->getLocation());
    }

    public function testSetCurrentLocationIfNotExistAndHaveTitle()
    {
        $locationEntity = new Location();
        $locationEntity->setName('test-name');
        $locationEntity->setLocation('test-location');

        $em = $this->getMock(EntityManager::class);
        $locationsRepository = $this->getMock(Locations::class);
        $locationsRepository->method('findOneBy')->willReturn(null);
        $em->method('getRepository')->willReturn($locationsRepository);

        $locations = new \EnjoysCMS\Core\Components\Detector\Locations($em);

        $route = new Route('test-route');
        $route->setDefault('_controller', ['App\Namespace', 'method']);
        $route->setOption('title', 'Main');
        $locations->setCurrentLocation($route);
        $this->assertSame('Main', $locations::getCurrentLocation()->getName());
        $this->assertSame('App\Namespace::method', $locations::getCurrentLocation()->getLocation());
    }
}
