<?php

namespace EnjoysCMS\Core\Components\AccessControl;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\RouteCollection;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ACLTwigExtension extends AbstractExtension
{
    /**
     * @var ACL
     */
    private ACL $acl;
    /**
     * @var RouteCollection
     */
    private RouteCollection $routeCollection;
    /**
     * @var LoggerInterface|null
     */
    private ?LoggerInterface $logger;

    private bool $disableCheck = false;


    public function __construct(
        ACL $acl,
        RouteCollection $routeCollection,
        LoggerInterface $logger = null
    ) {
        $this->acl = $acl;
        $this->routeCollection = $routeCollection;
        $this->logger = $logger;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('access', [$this, 'checkAccess'], ['is_safe' => ['html']]),
            new TwigFunction('access2route', [$this, 'checkAccessToRoute'], ['is_safe' => ['html']]),
            new TwigFunction('accessInRoutes', [$this, 'checkAccessToRoutes'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function checkAccess(string $action, ?string $comment = null): bool
    {
        if ($this->isDisableCheck()) {
            return true;
        }
        return $this->acl->access($action, (string)$comment);
    }

    public function checkAccessToRoutes(array $routes): bool
    {
        if ($this->isDisableCheck()) {
            return true;
        }

        $result = [false];
        foreach ($routes as $route) {
            $result[] = $this->checkAccessToRoute($route);
        }

        return in_array(true, $result);
    }

    public function checkAccessToRoute(string $route): bool
    {
        if ($this->isDisableCheck()) {
            return true;
        }

        try {
            $routeInfo = $this->routeCollection->get($route);
            if ($routeInfo === null) {
                throw new InvalidArgumentException(sprintf('Не найден маршрут %s', $route));
            }
            $action = $routeInfo->getDefault('_controller');
            if (is_array($action)) {
                $action = implode('::', $routeInfo->getDefault('_controller'));
            }
            $comment = $routeInfo->getOption('aclComment');
            return $this->checkAccess($action, $comment);
        } catch (InvalidArgumentException | OptimisticLockException | ORMException $e) {
            if ($this->logger === null) {
                trigger_error($e->getMessage(), E_USER_WARNING);
            } else {
                $this->logger->warning($e->getMessage(), [$e->getTraceAsString()]);
            }
            return false;
        }
    }

    /**
     * @return bool
     */
    public function isDisableCheck(): bool
    {
        return $this->disableCheck;
    }

    /**
     * @param bool $disableCheck
     */
    public function setDisableCheck(bool $disableCheck): void
    {
        $this->disableCheck = $disableCheck;
    }
}
