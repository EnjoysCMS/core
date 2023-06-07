<?php

namespace EnjoysCMS\Core\Block\Loader;

use EnjoysCMS\Core\Block\BlockCollection;
use InvalidArgumentException;
use LogicException;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Config\Resource\FileResource;

use function count;
use function defined;
use function function_exists;
use function in_array;

use function is_string;

use const PATHINFO_EXTENSION;
use const T_CLASS;
use const T_COMMENT;
use const T_DOC_COMMENT;
use const T_DOUBLE_COLON;
use const T_INLINE_HTML;
use const T_NAME_QUALIFIED;
use const T_NAMESPACE;
use const T_NEW;
use const T_NS_SEPARATOR;
use const T_STRING;
use const T_WHITESPACE;

class AnnotationFileLoader extends FileLoader
{
    protected AnnotationClassLoader $loader;

    public function __construct(FileLocatorInterface $locator, AnnotationClassLoader $loader)
    {
        if (!function_exists('token_get_all')) {
            throw new LogicException('The Tokenizer extension is required for the routing annotation loaders.');
        }

        parent::__construct($locator);

        $this->loader = $loader;
    }

    /**
     * Loads from annotations from a file.
     *
     * @param  mixed  $resource
     * @param  string|null  $type
     * @return BlockCollection|null
     * @throws ReflectionException
     */
    public function load(mixed $resource, string $type = null): ?BlockCollection
    {
        $path = $this->locator->locate($resource);

        $collection = new BlockCollection();
        if ($class = $this->findClass($path)) {
            $reflectionClass = new ReflectionClass($class);
            if ($reflectionClass->isAbstract()) {
                return null;
            }

            $collection->addResource(new FileResource($path));
            $collection->addCollection($this->loader->load($class, $type));
        }

        gc_mem_caches();

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(mixed $resource, string $type = null): bool
    {
        return is_string($resource) && 'php' === pathinfo(
                $resource,
                PATHINFO_EXTENSION
            ) && (!$type || 'annotation' === $type);
    }

    /**
     * Returns the full class name for the first class in the file.
     */
    protected function findClass(string $file): string|false
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
                return $namespace.'\\'.$token[1];
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
