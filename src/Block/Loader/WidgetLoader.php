<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Block\Loader;

use Doctrine\Common\Annotations\Reader;
use EnjoysCMS\Core\Block\AbstractWidget;
use EnjoysCMS\Core\Block\Annotation\Widget;
use EnjoysCMS\Core\Block\Collection;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Finder\Finder;

class WidgetLoader extends AnnotationLoader
{

    public function __construct(
        private readonly Finder $finder,
        protected ?Reader $reader = null
    ) {
        parent::__construct(Widget::class, $this->finder, $this->reader);
    }


    /**
     * @throws ReflectionException
     */
    public function getCollection(): Collection
    {
        $collection = new Collection();

        foreach ($this->finder as $file) {
            /** @var class-string $class */
            if ($class = $this->findClass($file->getPathname())) {
                $reflectionClass = new ReflectionClass($class);

                if ($reflectionClass->isAbstract()) {
                    continue;
                }

                if (!$reflectionClass->isSubclassOf(AbstractWidget::class)
                ) {
                    continue;
                }

                foreach ($this->getAnnotations($reflectionClass) as $annotation) {
                    $annotation->setReflectionClass($reflectionClass);
                    $collection->addAnnotation($annotation);
                }
            }
        }


        return $collection;
    }

}
