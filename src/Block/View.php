<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Block;

use DI\DependencyException;
use DI\FactoryInterface;
use DI\NotFoundException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;
use EnjoysCMS\Core\Block\Entity\Block;
use EnjoysCMS\Core\Components\Detector\Locations;
use EnjoysCMS\Core\Components\Helpers\ACL;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;

class View
{
    private Repository\Block|EntityRepository $repository;
    private LoggerInterface $logger;
    private EntityManager $entityManager;


    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function __construct(private FactoryInterface $container)
    {
        $this->entityManager = $container->get(EntityManager::class);
        $this->repository = $this->entityManager->getRepository(Block::class);
        $this->logger = $container->get(LoggerInterface::class);
    }


    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function view(int|string $blockId): ?string
    {
        $block = $this->repository->find($blockId);

        if ($block === null) {
            $this->logger->notice(sprintf('Blocks: Not found block by id: %s', $blockId), debug_backtrace());
            return null;
        }

        if (
            ACL::access(
                $block->getBlockActionAcl(),
                ":Блок: Доступ к просмотру блока '{$block->getName()}'"
            ) === false
        ) {
            $this->logger->debug(
                sprintf("Blocks: Access not allowed to block: '%s'", $block->getName()),
                [
                    'id' => $block->getId(),
                    'class' => $block->getClassName(),
                    'name' => $block->getName(),
                ]
            );
            return null;
        }

        if (!in_array(Locations::getCurrentLocation()->getId(), $block->getLocationsIds())) {
            $this->logger->debug(sprintf('Blocks: Location not constrains: %s', $block->getId()), $block->getLocationsIds());
            return null;
        }

        return $this->container->make($block->getClassName(), ['block' => $block])->view();
    }
}
