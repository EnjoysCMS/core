<?php


namespace EnjoysCMS\Core\Components\WYSIWYG;


use Exception;
use Psr\Container\ContainerInterface;
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

    public static function getInstance($editorName, ContainerInterface $container): WYSIWYG
    {
        $twig = $container->get(Environment::class);
        try {
            $wysiwyg = new self($container->get($editorName), $twig);
        } catch (\Error $error) {
            $wysiwyg = new self(new NullEditor(), $twig);
            $container->get(LoggerInterface::class)->withName('WYSIWYG')->error($error->getMessage());
        }
        return $wysiwyg;
    }


    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function selector(string $selector): string
    {
        $twigTemplate = $this->editor->getTwigTemplate();
        if (!$this->twig->getLoader()->exists($twigTemplate)) {
            throw new Exception(sprintf("Нет шаблона в по указанному пути: %s", $twigTemplate));
        }
        return $this->twig->render(
            $twigTemplate,
            [
                'editor' => $this->editor,
                'selector' => $selector
            ]
        );
    }

    public function setTwig(Environment $twig): void
    {
        $this->twig = $twig;
    }
}
