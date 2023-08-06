<?php

namespace EnjoysCMS\Core\AccessControl\Extensions\Twig;

use EnjoysCMS\Core\AccessControl\AccessControl;
use Twig\Extension\AbstractExtension;
use Twig\Extension\ExtensionInterface;
use Twig\TwigFunction;

class AccessControlTwigExtension extends AbstractExtension
{

    private bool $disableCheck = false;

    public function __construct(
        private readonly AccessControl $accessControl
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('access', [$this, 'checkAccess'], ['is_safe' => ['html']])
        ];
    }

    public function checkAccess(string|array $action): bool
    {
        if ($this->isDisableCheck()) {
            return true;
        }

        $result = [false];
        foreach ((array)$action as $item) {
            /** @var string $item */
            $result[] = $this->accessControl->isAccess($item);
        }
        return in_array(true, $result, true);
    }

    public function isDisableCheck(): bool
    {
        return $this->disableCheck;
    }

    public function setDisableCheck(bool $disableCheck): AccessControlTwigExtension
    {
        $this->disableCheck = $disableCheck;
        return $this;
    }

}
