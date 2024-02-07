<?php

namespace EnjoysCMS\Core\Block;

use DI\FactoryInterface;
use EnjoysCMS\Core\Block\Entity\Widget;
use EnjoysCMS\Core\Block\Repository\Widgets;
use Psr\Log\LoggerInterface;
use Throwable;

class WidgetModel
{

    public function __construct(
        private readonly Widgets $widgetsRepository,
        private readonly FactoryInterface $factory,
        private readonly LoggerInterface $logger,
    ) {
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
            return $this->factory->make($class)->setEntity($widget)->view();
        } catch (Throwable $e) {
            $this->logger->error(sprintf('Widgets: Occurred Error: %s', $e->getMessage()), $e->getTrace());
            return $e->getMessage();
        }
    }
}
