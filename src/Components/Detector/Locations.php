<?php


namespace App\Components\Detector;


use App\Entities\Locations as Entity;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ObjectRepository;

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


    public function __construct(string $controller, EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->locationsRepository = $entityManager->getRepository(Entity::class);
        $this->setCurrentLocation($controller);
    }


    private function setCurrentLocation(string $controller): void
    {
        if (null === $entity = $this->locationsRepository->findOneBy(['location' => $controller])) {
            $entity = new Entity();
            $entity->setLocation($controller);

            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        }
        self::$currentLocation = $entity;
    }


    public static function getCurrentLocation()
    {
        return self::$currentLocation;
    }
}
