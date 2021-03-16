<?php


namespace App\Components\Blocks;


use App\Blocks\Custom;
use App\Components\Detector\Locations;
use App\Components\Helpers\ACL;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Twig\Environment;

class Blocks
{
    /**
     * @var \App\Entities\Blocks
     */
    private $bocksRepository;
    /**
     * @var Environment
     */
    private Environment $twig;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * Blocks constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager, Environment $twig, LoggerInterface $logger = null)
    {
        $this->bocksRepository = $entityManager->getRepository(\App\Entities\Blocks::class);
        $this->twig = $twig;
        $this->logger = $logger ?? new NullLogger();
    }



    public function getBlock(int $blockId): ?string
    {
        /** @var \App\Entities\Blocks $block */
        $block = $this->bocksRepository->find($blockId);

        if ($block === null) {
            $this->logger->notice(sprintf('Not found block by id: %s', $blockId), debug_backtrace());
            return null;
        }


        if (ACL::access($block->getBlockActionAcl(), ":Блок: Доступ к просмотру блока '{$block->getName()}'") === false) {
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

        /** @var AbstractBlock $obj */
        $class = $block->getClass();
        $obj = new $class($this->twig, $block);
        return $obj->view();
    }
}
