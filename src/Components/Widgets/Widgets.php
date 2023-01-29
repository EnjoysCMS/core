<?php

namespace EnjoysCMS\Core\Components\Widgets;

use DI\FactoryInterface;
use Doctrine\ORM\EntityManager;
use EnjoysCMS\Core\Entities\Widget;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use Twig\Environment;

class Widgets
{
    private \EnjoysCMS\Core\Repositories\Widgets $widgetsRepository;
    private LoggerInterface $logger;


    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __construct(private ContainerInterface $container)
    {
        $this->widgetsRepository = $container->get(EntityManager::class)->getRepository(Widget::class);
        $this->logger = $container->get(LoggerInterface::class);
    }



    public function getWidget(int $widgetId): ?string
    {
        /** @var Widget $widget */
        $widget = $this->widgetsRepository->find($widgetId);

        if ($widget === null) {
            $this->logger->notice(sprintf('Widgets: Not found widget by id: %s', $widgetId), debug_backtrace());
            return null;
        }

//        if (ACL::access(
//                $widget->getBlockActionAcl(),
//                ":Блок: Доступ к просмотру блока '{$widget->getName()}'"
//            ) === false) {
//            $this->logger->debug(
//                sprintf("Widgets: Access not allowed to widget: '%s'", $widget->getName()),
//                [
//                    'id' => $widget->getId(),
//                    'class' => $widget->getClass(),
//                    'name' => $widget->getName(),
//                ]
//            );
//            return null;
//        }

        try {
            $class = $widget->getClass();
            $obj = $this->container->get(FactoryInterface::class)->make($class, ['widget' => $widget]);
            return $obj->view();
        } catch (\Throwable $e) {
            $this->logger->error(sprintf('Widgets: Occurred Error: %s', $e->getMessage()), $e->getTrace());
            return $e->getMessage();
        }
    }
}
