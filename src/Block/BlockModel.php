<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Block;

use DI\DependencyException;
use DI\NotFoundException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Exception\NotSupported;
use EnjoysCMS\Core\Block\Entity\Block;
use EnjoysCMS\Core\Components\Detector\Locations;
use EnjoysCMS\Core\Components\Helpers\ACL;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;

class BlockModel
{
    private Repository\Block|EntityRepository $repository;


    /**
     * @throws NotSupported
     */
    public function __construct(
        private BlockFactory $blockFactory,
        private EntityManager $entityManager,
        private LoggerInterface $logger
    ) {
        $this->repository = $this->entityManager->getRepository(Block::class);
    }


    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function view(string $blockId): ?string
    {
        /** @var null|Block $block */
        $block = $this->repository->find($blockId);

        if ($block === null) {
            $this->logger->notice(sprintf('Not found block by id: %s', $blockId));
            return null;
        }

        if (
            ACL::access(
                $block->getBlockActionAcl(),
                ":Блок: Доступ к просмотру блока '{$block->getName()}'"
            ) === false
        ) {
            $this->logger->debug(
                sprintf("Access not allowed to block: '%s'", $block->getName()),
                [
                    'id' => $block->getId(),
                    'class' => $block->getClassName(),
                    'name' => $block->getName(),
                ]
            );
            return null;
        }

        if (!in_array(Locations::getCurrentLocation()->getId(), $block->getLocationsIds(), true)) {
            $this->logger->debug(sprintf('Location not constrains: %s', $block->getId()), $block->getLocationsIds());
            return null;
        }

        return $this->blockFactory
            ->create($block->getClassName())
            ->setEntity($block)
            ->view();
    }
}
