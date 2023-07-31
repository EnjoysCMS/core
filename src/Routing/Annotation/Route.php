<?php

namespace EnjoysCMS\Core\Routing\Annotation;


use Attribute;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * Annotation class for @Route().
 *
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"CLASS", "METHOD"})
 *
 */
#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class Route extends \Symfony\Component\Routing\Annotation\Route
{

    public function __construct(
        array|string $path = null,
        ?string $name = null,
        array $requirements = [],
        array $options = [],
        array $defaults = [],
        ?string $host = null,
        array|string $methods = [],
        array|string $schemes = [],
        ?string $condition = null,
        ?int $priority = null,
        string $locale = null,
        string $format = null,
        bool $utf8 = null,
        bool $stateless = null,
        ?string $env = null,
        private readonly ?string $title = null,
        private readonly ?string $comment = null,
        private readonly bool $needAuthorized = true,
        private readonly array $middlewares = [],

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
            $env
        );

        $this->setOptions(
            array_merge($this->getOptions(), [
                'middlewares' => $this->getMiddlewares(),
                'comment' => $this->getComment(),
                'title' => $this->getTitle(),
                'acl' => $this->isNeedAuthorized()
            ])
        );
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function isNeedAuthorized(): bool
    {
        return $this->needAuthorized;
    }

}
