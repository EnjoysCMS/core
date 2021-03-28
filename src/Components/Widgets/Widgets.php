<?php


namespace EnjoysCMS\Core\Components\Widgets;


use Doctrine\ORM\EntityManager;
use EnjoysCMS\Core\Components\Detector\Locations;
use EnjoysCMS\Core\Components\Helpers\ACL;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Twig\Environment;

class Widgets
{
    /**
     * @var \EnjoysCMS\Core\Entities\Widgets
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

    /**
     * Blocks constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager, Environment $twig, LoggerInterface $logger = null)
    {
        $this->widgetsRepository = $entityManager->getRepository(\EnjoysCMS\Core\Entities\Widgets::class);
        $this->twig = $twig;
        $this->logger = $logger ?? new NullLogger();
    }



    public function getWidget(int $widgetId): ?string
    {
        /** @var \EnjoysCMS\Core\Entities\Widgets $widget */
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
        $obj = new $class($this->twig, $widget);
        return $obj->view();
    }
}
