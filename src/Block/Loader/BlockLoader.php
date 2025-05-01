<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Block\Loader;

use EnjoysCMS\Core\Block\AbstractBlock;
use EnjoysCMS\Core\Block\BlockAttributes;
use EnjoysCMS\Core\Block\AttributesCollection;
use Error;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Finder\Finder;

class BlockLoader extends AttributesLoader
{

    public function __construct(
        private readonly Finder $finder
    ) {
        parent::__construct(BlockAttributes::class, $this->finder);
    }


    public function getCollection(): AttributesCollection
    {
        $collection = new AttributesCollection();

        foreach ($this->finder as $file) {
            /** @var class-string $class */
            if ($class = $this->findClass($file->getPathname())) {
                try {
                    $reflectionClass = new ReflectionClass($class);
                } catch (ReflectionException|Error) {
                    continue;
                }

                if ($reflectionClass->isAbstract()) {
                    continue;
                }

                if (!$reflectionClass->isSubclassOf(AbstractBlock::class)
                ) {
                    continue;
                }

                foreach ($this->getAnnotations($reflectionClass) as $annotation) {
                    $annotation->setReflectionClass($reflectionClass);
                    $collection->addAttributes($annotation);
                }
            }
        }


        return $collection;
    }


}
