<?php

namespace EnjoysCMS\Core\Block;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use EnjoysCMS\Core\Block\Entity\Widget;
use EnjoysCMS\Core\Block\Repository\Widgets;
use Psr\Log\LoggerInterface;
use Throwable;

class WidgetModel
{
    private Widgets $widgetsRepository;
    private LoggerInterface $logger;


    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws NotSupported
     */
    public function __construct(private readonly Container $container)
    {
        $this->widgetsRepository = $container->get(EntityManager::class)->getRepository(Widget::class);
        $this->logger = $container->get(LoggerInterface::class);
    }


    public function view(int $widgetId): ?string
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
            /** @var class-string<AbstractWidget> $class */
            $class = $widget->getClass();
            return $this->container->make($class)->setEntity($widget)->view();
        } catch (Throwable $e) {
            $this->logger->error(sprintf('Widgets: Occurred Error: %s', $e->getMessage()), $e->getTrace());
            return $e->getMessage();
        }
    }
}
