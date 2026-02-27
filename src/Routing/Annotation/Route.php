<?php

namespace EnjoysCMS\Core\Routing\Annotation;


use Attribute;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Symfony\Component\Routing\Attribute\DeprecatedAlias;

/**
 * Annotation class for @Route().
 *
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"CLASS", "METHOD"})
 *
 */
#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class Route extends \Symfony\Component\Routing\Attribute\Route
{
    public function __construct(
        array|string|null $path = null,
        ?string $name = null,
        array $requirements = [],
        array $options = [],
        array $defaults = [],
        ?string $host = null,
        array|string $methods = [],
        array|string $schemes = [],
        ?string $condition = null,
        ?int $priority = null,
        ?string $locale = null,
        ?string $format = null,
        ?bool $utf8 = null,
        ?bool $stateless = null,
        ?string $env = null,
        // custom fields
        ?string $title = null,
        ?string $comment = null,
        ?bool $needAuthorized = null,
        ?array $middlewares = null,
        array|string|null $groups = null,

    ) {
        parent::__construct(
            $path,
            $name,
            $requirements,
            $options,
            $defaults,
            $host,
            $methods,
            $schemes,
            $condition,
            $priority,
            $locale,
            $format,
            $utf8,
            $stateless,
            $env,
        );

        $options = $this->getOptions();
        $options['comment'] = $comment;
        $options['title'] = $title;

        if ($middlewares !== null) {
            $options['middlewares'] = $middlewares;
        }

        if ($needAuthorized !== null) {
            $options['acl'] = $needAuthorized;
        }

        if ($groups !== null) {
            $options['groups'] = (array)$groups;
        }

        $this->setOptions($options);
    }


    #[\Override]
    public function setHost(string $pattern): void
    {
        if ($this->isPublicProperty('host')) {
            $this->host = $pattern;
            return;
        }
        parent::setHost($pattern);
    }

    #[\Override]
    public function getHost(): ?string
    {
        if ($this->isPublicProperty('host')) {
            return $this->host;
        }
        return parent::getHost();
    }

    #[\Override]
    public function setName(string $name): void
    {
        if ($this->isPublicProperty('name')) {
            $this->name = $name;
            return;
        }
        parent::setName($name);
    }

    #[\Override]
    public function getName(): ?string
    {
        if ($this->isPublicProperty('name')) {
            return $this->name;
        }
        return parent::getName();
    }

    #[\Override]
    public function setRequirements(array $requirements): void
    {
        if ($this->isPublicProperty('requirements')) {
            $this->requirements = $requirements;
            return;
        }
        parent::setRequirements($requirements);
    }

    #[\Override]
    public function getRequirements(): array
    {
        if ($this->isPublicProperty('requirements')) {
            return $this->requirements;
        }
        return parent::getRequirements();
    }

    #[\Override]
    public function setOptions(array $options): void
    {
        if ($this->isPublicProperty('options')) {
            $this->options = $options;
            return;
        }
        parent::setOptions($options);
    }

    #[\Override]
    public function getOptions(): array
    {
        if ($this->isPublicProperty('options')) {
            return $this->options;
        }
        return parent::getOptions();
    }

    #[\Override]
    public function setDefaults(array $defaults): void
    {
        if ($this->isPublicProperty('defaults')) {
            $this->defaults = $defaults;
            return;
        }
        parent::setDefaults($defaults);
    }

    #[\Override]
    public function getDefaults(): array
    {
        if ($this->isPublicProperty('defaults')) {
            return $this->defaults;
        }
        return parent::getDefaults();
    }

    #[\Override]
    public function setSchemes(array|string $schemes): void
    {
        if ($this->isPublicProperty('schemes')) {
            $this->schemes = (array)$schemes;
            return;
        }
        parent::setSchemes($schemes);
    }

    #[\Override]
    public function getSchemes(): array
    {
        if ($this->isPublicProperty('schemes')) {
            return $this->schemes;
        }
        return parent::getSchemes();
    }

    #[\Override]
    public function setMethods(array|string $methods): void
    {
        if ($this->isPublicProperty('methods')) {
            $this->methods = (array)$methods;
            return;
        }
        parent::setMethods($methods);
    }

    #[\Override]
    public function getMethods(): array
    {
        if ($this->isPublicProperty('methods')) {
            return $this->methods;
        }
        return parent::getMethods();
    }

    #[\Override]
    public function setCondition(?string $condition): void
    {
        if ($this->isPublicProperty('condition')) {
            $this->condition = $condition;
            return;
        }
        parent::setCondition($condition);
    }

    #[\Override]
    public function getCondition(): ?string
    {
        if ($this->isPublicProperty('condition')) {
            return $this->condition;
        }
        return parent::getCondition();
    }

    #[\Override]
    public function setPriority(int $priority): void
    {
        if ($this->isPublicProperty('priority')) {
            $this->priority = $priority;
            return;
        }
        parent::setPriority($priority);
    }

    #[\Override]
    public function getPriority(): ?int
    {
        if ($this->isPublicProperty('priority')) {
            return $this->priority;
        }
        return parent::getPriority();
    }

    #[\Override]
    public function setEnv(?string $env): void
    {
        if ($this->isPublicProperty('envs')) {
            $this->envs = (array)$env;
            return;
        }
        parent::setEnv($env);
    }

    #[\Override]
    public function getEnv(): ?string
    {
        if ($this->isPublicProperty('envs')) {
            if (!$this->envs) {
                return null;
            }
            if (\count($this->envs) > 1) {
                throw new \LogicException(
                    \sprintf(
                        'The "env" property has %d environments. Use "getEnvs()" to get all of them.',
                        \count($this->envs),
                    ),
                );
            }
            return $this->envs[0];
        }
        return parent::getEnv();
    }

    public function getEnvs(): ?array
    {
        if ($this->isPublicProperty('envs')) {
            return $this->envs;
        }
        $envs = parent::getEnv();
        return $envs ? [$envs] : null;
    }

    #[\Override]
    public function getAliases(): array
    {
        if ($this->isPublicProperty('aliases')) {
            return $this->aliases;
        }
        return parent::getAliases();
    }

    /**
     * @param string|DeprecatedAlias|(string|DeprecatedAlias)[] $aliases
     */
    #[\Override]
    public function setAliases(string|DeprecatedAlias|array $aliases): void
    {
        if ($this->isPublicProperty('aliases')) {
            $this->aliases = \is_array($aliases) ? $aliases : [$aliases];
            return;
        }
        parent::setAliases($aliases);
    }


    private function isPublicProperty(string $property): bool
    {
        if (!property_exists($this, $property)) {
            return false;
        }

        try {
            $reflection = new \ReflectionProperty($this, $property);
            return $reflection->isPublic();
        } catch (\ReflectionException $e) {
            return false;
        }
    }

}
