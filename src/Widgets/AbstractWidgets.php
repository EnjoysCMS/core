<?php

namespace EnjoysCMS\Core\Widgets;

use Enjoys\Traits\Options;
use EnjoysCMS\Core\Entities\Widget as Entity;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Twig\Environment;

abstract class AbstractWidgets
{

    protected Environment $twig;
    protected array $options = [];

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(
        private readonly ContainerInterface $container,
        protected Entity $widget
    ) {
        $this->twig = $container->get(Environment::class);
    }


    abstract public function view();

    public static function getMeta(): ?array
    {
        return null;
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setOption(string $key, $value): self
    {
        $this->options[$key] = $value;
        return $this;
    }

    /**
     *
     * @param string $key
     * @param mixed $defaults
     * @return mixed
     * @since 1.3.0 добавлен флаг $useInternalMethods
     */
    public function getOption(string $key, $defaults = null)
    {

        if (isset($this->options[$key])) {
            return $this->options[$key];
        }
        return $defaults;
    }

    /**
     *
     * @param array<mixed> $options
     * @return $this
     * @since 1.3.0 добавлен флаг $useInternalMethods
     */
    public function setOptions(array $options = []): self
    {
        foreach ($options as $key => $value) {
            $this->setOption((string)$key, $value);
        }
        return $this;
    }

    /**
     *
     * @return array<mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

}
