<?php


namespace EnjoysCMS\Core\Components\Widgets;


use DI\FactoryInterface;
use Doctrine\ORM\EntityManager;
use EnjoysCMS\Core\Components\Detector\Locations;
use EnjoysCMS\Core\Components\Helpers\ACL;
use EnjoysCMS\Core\Entities\Widget;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Twig\Environment;

class Widgets
{
    /**
     * @var Widget
     */
    private $widgetsRepository;
    /**
     * @var Environment
     */
    private Environment $twig;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    private ContainerInterface $container;

    /**
     * Blocks constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(ContainerInterface $container)
    {
        $this->widgetsRepository = $container->get(EntityManager::class)->getRepository(Widget::class);
        $this->twig = $container->get(Environment::class);
        $this->logger = $container->get(LoggerInterface::class);
        $this->container = $container;
    }



    public function getWidget(int $widgetId): ?string
    {
        /** @var Widget $widget */
        $widget = $this->widgetsRepository->find($widgetId);

        if ($widget === null) {
            $this->logger->notice(sprintf('Not found widget by id: %s', $widgetId), debug_backtrace());
            return null;
        }
//
//
//        if (ACL::access(
//                $widget->getBlockActionAcl(),
//                ":Блок: Доступ к просмотру блока '{$widget->getName()}'"
//            ) === false) {
//            $this->logger->debug(
//                sprintf("Access not allowed to widget: '%s'", $widget->getName()),
//                [
//                    'id' => $widget->getId(),
//                    'class' => $widget->getClass(),
//                    'name' => $widget->getName(),
//                ]
//            );
//            return null;
//        }
//
//
//
//        /**
//         *
//         *
//         * @var AbstractWidgets $obj
//         */
        $class = $widget->getClass();
        $obj = $this->container->get(FactoryInterface::class)->make($class, ['widget' => $widget]);
        return $obj->view();
    }
}
