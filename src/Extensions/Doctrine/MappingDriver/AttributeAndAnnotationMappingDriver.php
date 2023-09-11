<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Extensions\Doctrine\MappingDriver;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\PsrCachedReader;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\ORM\Mapping\Driver\CompatibilityAnnotationDriver;
use Doctrine\ORM\Mapping\MappingException;
use Doctrine\ORM\ORMSetup;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Psr\Cache\CacheItemPoolInterface;

/**
 * [stackoverflow] Doctrine ORM 2.9 use both AnnotationDriver and AttributeDriver to parse entity metadata
 * @see https://stackoverflow.com/questions/68195822/doctrine-orm-2-9-use-both-annotationdriver-and-attributedriver-to-parse-entity-m
 */
class AttributeAndAnnotationMappingDriver extends CompatibilityAnnotationDriver
{
    private AttributeDriver $attributeDriver;
    private AnnotationDriver $annotationDriver;

    public function __construct(
        array $paths = [],
        ?CacheItemPoolInterface $cache = null,
        bool $reportFieldsWhereDeclared = true
    ) {
        $this->attributeDriver = new AttributeDriver($paths, $reportFieldsWhereDeclared);


        $reader = new AnnotationReader();
        if ($cache !== null) {
            $reader = new PsrCachedReader($reader, $cache);
        }
        $this->annotationDriver = new AnnotationDriver($reader, $paths, $reportFieldsWhereDeclared);
    }

    /**
     * @throws MappingException
     */
    public function loadMetadataForClass(string $className, ClassMetadata $metadata): void
    {
        try {
            $this->attributeDriver->loadMetadataForClass($className, $metadata);
            return;
        } catch (MappingException $e) {
            if (!preg_match('/^Class(.)*$/', $e->getMessage())) {
                throw $e;
            }
        }
        $this->annotationDriver->loadMetadataForClass($className, $metadata);
    }


    /**
     * @throws \Doctrine\Persistence\Mapping\MappingException
     */
    public function isTransient($className): bool
    {
        if (in_array($className, $this->attributeDriver->getAllClassNames(), true)) {
            return $this->attributeDriver->isTransient($className);
        }
        return  $this->annotationDriver->isTransient($className);
    }

    /**
     * @throws \Doctrine\Persistence\Mapping\MappingException
     */
    public function getAllClassNames(): array
    {
        return array_merge(
            $this->annotationDriver->getAllClassNames(),
            $this->attributeDriver->getAllClassNames()
        );
    }
}
