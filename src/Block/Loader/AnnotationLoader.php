<?php

namespace EnjoysCMS\Core\Block\Loader;

use Doctrine\Common\Annotations\Reader;
use EnjoysCMS\Core\Block\Annotation\Annotation;
use EnjoysCMS\Core\Block\Collection;
use InvalidArgumentException;
use ReflectionAttribute;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

abstract class AnnotationLoader
{


    /**
     * @param class-string<Annotation> $annotationClass
     * @param Finder $finder
     * @param Reader|null $reader
     */
    public function __construct(
        private readonly string $annotationClass,
        private readonly Finder $finder,
        protected ?Reader $reader = null,
    )
    {
        $this->finder->files()->name('/\.php$/');
    }

    abstract public function getCollection(): Collection;

    /**
     * @param ReflectionClass $reflection
     * @return iterable<int, Annotation>
     */
    final protected function getAnnotations(ReflectionClass $reflection): iterable
    {
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


    /**
     * Returns the full class name for the first class in the file.
     * @param string $file
     * @return non-empty-string|class-string|false
     */
    final protected function findClass(string $file): false|string
    {
        $class = false;
        $namespace = false;
        $tokens = token_get_all(file_get_contents($file));

        if (1 === count($tokens) && T_INLINE_HTML === $tokens[0][0]) {
            throw new InvalidArgumentException(
                sprintf(
                    'The file "%s" does not contain PHP code. Did you forgot to add the "<?php" start tag at the beginning of the file?',
                    $file
                )
            );
        }

        $nsTokens = [T_NS_SEPARATOR => true, T_STRING => true];
        if (defined('T_NAME_QUALIFIED')) {
            $nsTokens[T_NAME_QUALIFIED] = true;
        }
        for ($i = 0; isset($tokens[$i]); ++$i) {
            $token = $tokens[$i];
            if (!isset($token[1])) {
                continue;
            }

            if (true === $class && T_STRING === $token[0]) {
                return $namespace . '\\' . $token[1];
            }

            if (true === $namespace && isset($nsTokens[$token[0]])) {
                $namespace = $token[1];
                while (isset($tokens[++$i][1], $nsTokens[$tokens[$i][0]])) {
                    $namespace .= $tokens[$i][1];
                }
                $token = $tokens[$i];
            }

            if (T_CLASS === $token[0]) {
                // Skip usage of ::class constant and anonymous classes
                $skipClassToken = false;
                for ($j = $i - 1; $j > 0; --$j) {
                    if (!isset($tokens[$j][1])) {
                        if ('(' === $tokens[$j] || ',' === $tokens[$j]) {
                            $skipClassToken = true;
                        }
                        break;
                    }

                    if (T_DOUBLE_COLON === $tokens[$j][0] || T_NEW === $tokens[$j][0]) {
                        $skipClassToken = true;
                        break;
                    } elseif (!in_array($tokens[$j][0], [T_WHITESPACE, T_DOC_COMMENT, T_COMMENT])) {
                        break;
                    }
                }

                if (!$skipClassToken) {
                    $class = true;
                }
            }

            if (T_NAMESPACE === $token[0]) {
                $namespace = true;
            }
        }

        return false;
    }
}
