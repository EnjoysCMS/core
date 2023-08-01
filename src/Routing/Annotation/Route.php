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
        // custom fields
        ?string $title = null,
        ?string $comment = null,
        ?bool $needAuthorized = null,
        ?array $middlewares = null

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

        $options = $this->getOptions();
        $options['comment'] = $comment;
        $options['title'] = $title;

        if ($middlewares !== null) {
            $options['middlewares'] = $middlewares;
        }

        if ($needAuthorized !== null) {
            $options['acl'] = $needAuthorized;
        }

        $this->setOptions($options);
    }


}
