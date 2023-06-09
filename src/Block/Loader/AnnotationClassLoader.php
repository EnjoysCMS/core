<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Block\Loader;

use Doctrine\Common\Annotations\Reader;
use EnjoysCMS\Core\Block\AbstractBlock;
use EnjoysCMS\Core\Block\Annotation\Block as BlockAnnotation;
use EnjoysCMS\Core\Block\Collection;
use InvalidArgumentException;
use ReflectionAttribute;
use ReflectionClass;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;

use function is_string;

class AnnotationClassLoader implements LoaderInterface
{
    /**
     * @var string
     */
    protected string $annotationClass = BlockAnnotation::class;
    protected LoaderResolverInterface $resolver;

    public function __construct(protected ?Reader $reader = null)
    {
    }

    public function getResolver(): LoaderResolverInterface
    {
        return $this->resolver;
    }

    public function setResolver(LoaderResolverInterface $resolver): void
    {
        $this->resolver = $resolver;
    }

    /**
     * Sets the annotation class to read route properties from.
     */
    public function setAnnotationClass(string $class): void
    {
        $this->annotationClass = $class;
    }

    /**
     * @param mixed $resource
     * @param string|null $type
     * @return Collection
     */
    public function load(mixed $resource, string $type = null): Collection
    {
        if (!class_exists($resource)) {
            throw new InvalidArgumentException(sprintf('Class "%s" does not exist.', $resource));
        }

        $class = new ReflectionClass($resource);
        if ($class->isAbstract()) {
            throw new InvalidArgumentException(
                sprintf('Annotations from class "%s" cannot be read as it is abstract.', $class->getName())
            );
        }

        $collection = new Collection();

        foreach ($this->getAnnotations($class) as $annot) {
            $annot->setReflectionClass($class);
            $collection->addBlockAnnotation($annot);
        }


        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(mixed $resource, string $type = null): bool
    {
        return is_string($resource) && preg_match(
                '/^(?:\\\\?[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)+$/',
                $resource
            ) && (!$type || 'annotation' === $type);
    }

    /**
     * @param ReflectionClass $reflection
     * @return iterable<int, BlockAnnotation>
     */
    private function getAnnotations(ReflectionClass $reflection): iterable
    {
        if (!$reflection->isSubclassOf(AbstractBlock::class)
        ) {
            return;
        }

        foreach (
            $reflection->getAttributes(
                $this->annotationClass,
                ReflectionAttribute::IS_INSTANCEOF
            ) as $attribute
        ) {
            yield $attribute->newInstance();
        }

        if (!$this->reader) {
            return;
        }

        $annotations = $this->reader->getClassAnnotations($reflection);

        foreach ($annotations as $annotation) {
            if ($annotation instanceof $this->annotationClass) {
                yield $annotation;
            }
        }
    }


}
