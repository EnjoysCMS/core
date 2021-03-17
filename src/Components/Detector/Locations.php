<?php


namespace EnjoysCMS\Core\Components\Detector;


use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ObjectRepository;
use EnjoysCMS\Core\Entities\Locations as Entity;
use Symfony\Component\Routing\Route;

class Locations
{
    private static Entity $currentLocation;
    /**
     * @var EntityManager
     */
    private EntityManager $entityManager;
    /**
     * @var ObjectRepository
     */
    private $locationsRepository;


    public function __construct(Route $route, EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->locationsRepository = $entityManager->getRepository(Entity::class);
        $this->setCurrentLocation($route);
    }


    private function setCurrentLocation(Route $route): void
    {
        $controller = implode('::', $route->getDefault('_controller'));

        if (null === $entity = $this->locationsRepository->findOneBy(['location' => $controller])) {
            $entity = new Entity();
            $entity->setLocation($controller);
            $entity->setName($route->getOption('routeName') ?? $controller);
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        }
        self::$currentLocation = $entity;
    }


    public static function getCurrentLocation(): Entity
    {
        return self::$currentLocation;
    }
}
