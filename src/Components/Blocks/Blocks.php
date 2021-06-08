<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Components\Blocks;


use DI\DependencyException;
use DI\FactoryInterface;
use DI\NotFoundException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;
use EnjoysCMS\Core\Components\Detector\Locations;
use EnjoysCMS\Core\Components\Helpers\ACL;
use EnjoysCMS\Core\Entities\Block;
use Psr\Log\LoggerInterface;
use Twig\Environment;

class Blocks
{

    private ObjectRepository|EntityRepository $bocksRepository;
    private LoggerInterface $logger;
    private EntityManager $entityManager;


    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function __construct(private FactoryInterface $container)
    {
        $this->entityManager = $container->get(EntityManager::class);
        $this->bocksRepository = $this->entityManager->getRepository(Block::class);
        //  $this->twig = $container->get(Environment::class);
        $this->logger = $container->get(LoggerInterface::class)->withName('Blocks');
    }


    /**
     * @param int|string $id
     * @return Block|null
     */
    private function findBlockEntity(int|string $id): ?Block
    {
        if (is_numeric($id)) {
            return $this->bocksRepository->find($id);
        }
        return $this->bocksRepository->findOneBy(['alias' => $id]);
    }


    /**
     * @param int|string $blockId
     * @return string|null
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function getBlock(int|string $blockId): ?string
    {
        $block = $this->findBlockEntity($blockId);


        if ($block === null) {
            $this->logger->notice(sprintf('Not found block by id: %s', $blockId), debug_backtrace());
            return null;
        }


        if (ACL::access(
                $block->getBlockActionAcl(),
                ":Блок: Доступ к просмотру блока '{$block->getName()}'"
            ) === false) {
            $this->logger->debug(
                sprintf("Access not allowed to block: '%s'", $block->getName()),
                [
                    'id' => $block->getId(),
                    'class' => $block->getClass(),
                    'name' => $block->getName(),
                ]
            );
            return null;
        }

        if (!in_array(Locations::getCurrentLocation()->getId(), $block->getLocationsIds())) {
            $this->logger->debug(sprintf('Location not constrains: %s', $block->getId()), $block->getLocationsIds());
            return null;
        }

        return $this->container->make($block->getClass(), ['block' => $block])->view();
    }
}
