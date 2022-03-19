<?php


namespace EnjoysCMS\Core\Components\Detector;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ObjectRepository;
use EnjoysCMS\Core\Entities\Location as Entity;
use Symfony\Component\Routing\Route;

class Locations
{
    private static Entity $currentLocation;

    private ObjectRepository|EntityRepository $locationsRepository;

    public function __construct(private EntityManager $entityManager)
    {
        $this->locationsRepository = $entityManager->getRepository(Entity::class);
    }


    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function setCurrentLocation(Route $route): void
    {
        $controller = $route->getDefault('_controller');
        if (is_array($controller)) {
            $controller = implode('::', $controller);
        }

        /** @var Entity $entity */
        if (null === $entity = $this->locationsRepository->findOneBy(['location' => $controller])) {
            $entity = new Entity();
            $entity->setLocation($controller);
            $entity->setName($route->getOption('title') ?? $controller);
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
