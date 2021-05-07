<?php


namespace EnjoysCMS\Core\Components\Blocks;


use DI\FactoryInterface;
use Doctrine\ORM\EntityManager;
use EnjoysCMS\Core\Components\Detector\Locations;
use EnjoysCMS\Core\Components\Helpers\ACL;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Twig\Environment;

class Blocks
{
    /**
     * @var \EnjoysCMS\Core\Entities\Blocks
     */
    private $bocksRepository;
    /**
     * @var Environment
     */
    // private Environment $twig;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    private FactoryInterface $container;


    public function __construct(FactoryInterface $container)
    {
        $this->entityManager = $container->get(EntityManager::class);
        $this->bocksRepository = $this->entityManager->getRepository(\EnjoysCMS\Core\Entities\Blocks::class);
        //  $this->twig = $container->get(Environment::class);
        $this->container = $container;
        $this->logger = $container->get(LoggerInterface::class)->withName('Blocks');
    }


    public function getBlock(int $blockId): ?string
    {
        /**
         *
         *
         * @var \EnjoysCMS\Core\Entities\Blocks $block
         */
        $block = $this->bocksRepository->find($blockId);

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
