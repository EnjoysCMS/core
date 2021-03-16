<?php


namespace EnjoysCMS\Core\Components\WYSIWYG;


use Exception;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

class WYSIWYG
{


    /**
     * @var Environment
     */
    private Environment $twig;
    /**
     * @var WysiwygInterface
     */
    private WysiwygInterface $editor;

    public function __construct(WysiwygInterface $editor)
    {
        $this->twig = new Environment(new FilesystemLoader());
        $this->editor = $editor;
    }

    /**
     * @param string $selector
     * @param string $editor
     * @param string $mode
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    public function selector(string $selector)
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

    /**
     * @param Environment $twig
     */
    public function setTwig(Environment $twig): void
    {
        $this->twig = $twig;
    }
}
