<?php

namespace EnjoysCMS\Core\Components\WYSIWYG;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Exception;
use Psr\Log\LoggerInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class WYSIWYG
{
    public function __construct(private WysiwygInterface $editor, private Environment $twig)
    {
    }

    /**
     * @throws NotFoundException
     * @throws DependencyException
     */
    public static function getInstance(string|null $editorName, Container $container): ?WYSIWYG
    {
        if (is_null($editorName)) {
            return null;
        }

        $twig = $container->get(Environment::class);
        try {
            $wysiwyg = new self($container->make($editorName), $twig);
        } catch (\Error $error) {
            $wysiwyg = new self(new NullEditor(), $twig);
            $container->get(LoggerInterface::class)->error($error->getMessage());
        }

        return $wysiwyg;
    }


    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws Exception
     */
    public function selector(string $selector): string
    {
        $twigTemplate = $this->editor->getTwigTemplate();
        if (!$this->twig->getLoader()->exists($twigTemplate)) {
            throw new Exception(
                sprintf("WYSIWYG (%s): Нет шаблона в по указанному пути: %s", get_class($this->editor), $twigTemplate)
            );
        }
        return $this->twig->render(
            $twigTemplate,
            [
                'editor' => $this->editor,
                'selector' => $selector
            ]
        );
    }

    public function getEditor(): WysiwygInterface
    {
        return $this->editor;
    }

    public function setTwig(Environment $twig): void
    {
        $this->twig = $twig;
    }
}
