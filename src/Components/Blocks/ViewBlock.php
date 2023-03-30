<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Components\Blocks;

use DI\DependencyException;
use DI\NotFoundException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use EnjoysCMS\Core\Components\Detector\Locations;
use EnjoysCMS\Core\Components\Helpers\ACL;
use EnjoysCMS\Core\Entities\Block;
use EnjoysCMS\Core\Repositories\BlockRepository;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;

class ViewBlock
{
    private BlockRepository|EntityRepository $blockRepository;

    public function __construct(private EntityManager $em, private LoggerInterface $logger)
    {
        $this->blockRepository = $this->em->getRepository(Block::class);
    }



    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getBlock(int|string $blockId): ?string
    {
        $block = $this->blockRepository->find($blockId);

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
                    'class' => $block->getClass(),
                    'name' => $block->getName(),
                ]
            );
            return null;
        }

        if (!in_array(Locations::getCurrentLocation()->getId(), $block->getLocationsIds())) {
            $this->logger->debug(sprintf('Blocks: Location not constrains: %s', $block->getId()), $block->getLocationsIds());
            return null;
        }

        return $this->container->make($block->getClass(), ['block' => $block])->view();
    }
}
