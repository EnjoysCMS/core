<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Block;

use DI\DependencyException;
use DI\NotFoundException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use EnjoysCMS\Core\AccessControl\AccessControl;
use EnjoysCMS\Core\Block\Entity\Block;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class BlockModel
{
    private Repository\Block|EntityRepository $repository;


    /**
     * @throws NotSupported
     */
    public function __construct(
        private readonly BlockFactory $blockFactory,
        private readonly EntityManager $entityManager,
        private readonly AccessControl $accessControl,
        private readonly ServerRequestInterface $request,
        private readonly LoggerInterface $logger
    ) {
        $this->repository = $this->entityManager->getRepository(Block::class);
    }


    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function view(string $blockId): ?string
    {
        /** @var null|Block $block */
        $block = $this->repository->find($blockId);

        if ($block === null) {
            $this->logger->notice(sprintf('Not found block by id: %s', $blockId));
            return null;
        }

        if ($this->accessControl->isAccess($block->getId()) === false) {
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

        if (!in_array(
            implode('::', (array)$this->request->getAttribute('_route')->getDefault('_controller')),
            $block->getLocationsValues(),
            true
        )) {
            $this->logger->debug(sprintf('Location not constrains: %s', $block->getId()), $block->getLocationsValues());
            return null;
        }

        return $this->blockFactory
            ->create($block->getClassName())
            ->setEntity($block)
            ->view();
    }
}
